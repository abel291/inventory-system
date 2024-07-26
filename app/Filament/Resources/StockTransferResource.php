<?php

namespace App\Filament\Resources;

use App\Enums\StockStatuEnum;
use App\Filament\Resources\StockTransferResource\Pages;
use App\Filament\Resources\StockTransferResource\Pages\ViewStockTransfer;
use App\Filament\Resources\StockTransferResource\RelationManagers;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransfer;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Grid;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockTransferResource extends Resource
{
    protected static ?string $model = StockTransfer::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';

    public static ?string $label = 'Traslado';

    protected static ?string $pluralModelLabel = 'Traslados';

    protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('Ubicacions')
                    ->schema([
                        Forms\Components\Select::make('location_from_id')
                            ->live()
                            ->options(
                                Location::select('id', 'name', 'type')->active()->get()->pluck('nameType', 'id')
                            )
                            ->preload(1)
                            ->afterStateUpdated(function (Set $set) {
                                $set('location_to_id', null);
                                $set('stockTransferProduct', []);
                            })
                            ->label('Origen')->required(),

                        Forms\Components\Select::make('location_to_id')
                            ->live()
                            ->options(function (Forms\Get $get) {
                                return Location::select('id', 'name', 'type')
                                    ->whereNot('id', $get('location_from_id'))
                                    ->active()
                                    ->get()
                                    ->pluck('nameType', 'id');
                            })
                            ->label('Destinos')->required(),

                    ]),


                Forms\Components\TextInput::make('barcode')
                    ->hidden(fn (Get $get): bool => (!$get('location_from_id') || !$get('location_to_id')))
                    ->live()
                    ->label('')
                    ->placeholder('Codigo de barra')
                    ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                        self::formHandelInputBarCode($state, $get, $set);
                    })
                    ->dehydrated(false),

                Forms\Components\Repeater::make('stockTransferProduct')
                    ->hidden(fn (Get $get): bool => (!$get('location_from_id') || !$get('location_to_id')))
                    ->relationship()
                    ->required()
                    ->label('')
                    ->addActionLabel('Añadir producto')
                    ->default([])
                    ->columnSpanFull()
                    ->columns(6)
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Nombre del producto')
                            ->live()
                            ->placeholder('Codigo de barra o nombre del producto')
                            ->relationship(
                                name: 'product',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                    ->whereHas('locations', function (Builder $query) use ($get) {
                                        $query->where('location_id', $get('../../location_from_id'));
                                    }),
                            )
                            ->searchable(['name', 'barcode'])
                            ->getOptionLabelFromRecordUsing(function (Product $record) {
                                return "{$record->nameBarcodePrice}";
                            })
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {

                                if (!$state) {
                                    $set('stock', null);
                                    $set('quantity', null);
                                    return;
                                }
                                $stock = Stock::where([
                                    ['product_id', $state],
                                    ['location_id', $get('../../location_from_id')],
                                ])->first();

                                $set('stock', $stock ? $stock->quantity : 0);
                                $set('quantity', null);
                            })
                            ->native(false)
                            ->required()
                            ->columnSpan(4),

                        Forms\Components\TextInput::make('stock')
                            ->label('Existencia')
                            ->disabled()
                            ->numeric(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->default(1)
                            ->required()
                            ->numeric()
                            ->maxValue(fn (Get $get) => ($get('stock')))
                            ->helperText(fn (Get $get) => ("Maximo " . $get('stock')))
                            ->minValue(1),
                    ]),


            ])->columns(3);
    }

    public static function formHandelInputBarCode(?string $state, Get $get, Set $set)
    {
        if (!$state) {
            return;
        }
        $stock = Stock::with('product')
            ->where('location_id', $get('location_from_id'))
            ->where('quantity', '>', 0)
            ->whereRelation('product', 'barcode', $state)->first();
        $set('barcode', null);

        if (!$stock) {
            Notification::make()
                ->title('El producto no pudo ser encontrado o no tiene existencia disponible en la ubicacion selecionada')
                ->danger()
                ->send();
            return;
        }
        $listProducts = collect($get('stockTransferProduct'));
        $isInList = $listProducts->firstWhere('product_id', $stock->product_id);

        if ($isInList) {
            $listProducts = $listProducts->map(function ($item) use ($stock) {
                if ($item['product_id'] == $stock->product_id) {
                    $item['quantity'] += 1;
                }
                return $item;
            });
            $set('stockTransferProduct', $listProducts->toArray());

            Notification::make()
                ->title('El producto ya esta en la lista , se le agrego +1')
                ->info()
                ->send();
            return;
        }

        $set('stockTransferProduct', [
            [
                'product_id' => $stock->product_id,
                'stock' => $stock->quantity,
                'quantity' => 1,
            ],
            ...$get('stockTransferProduct')
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('id')
                    ->label('Codigo')
                    ->searchable(),
                Tables\Columns\TextColumn::make('userRequest.name')
                    ->label('Solicitante')
                    ->searchable(),
                Tables\Columns\TextColumn::make('locationFrom.nameType')
                    ->label('Destino')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('locationTo.nameType')
                    ->label('Origen')
                    ->badge()
                    ->searchable(),
                Tables\Columns\TextColumn::make('products_count')->counts('products')
                    ->numeric()
                    ->label('Productos'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    // ->description(function (StockTransfer $record) {
                    //     return ($record->status == StockStatuEnum::ACCEPTED) ? $record->userApprove->name : '';
                    // })
                    ->badge(),
                Tables\Columns\TextColumn::make('created_at')->label('Fecha de creacion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label('Fecha de modificacion')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
            ])
            ->filters([
                \Filament\Tables\Filters\SelectFilter::make('location_to_id')->label('Destino')
                    ->options(Location::active()->get()->pluck('nameType', 'id'))
                    ->columnSpan(2)
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('location_from_id')->label('Origen')
                    ->options(Location::active()->get()->pluck('nameType', 'id'))
                    ->columnSpan(2)
                    ->preload(),
                \Filament\Tables\Filters\SelectFilter::make('location_to_id')->label('Destino')
                    ->options(Location::active()->get()->pluck('nameType', 'id'))
                    ->columnSpan(2)
                    ->preload()
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()->label('Ver productos'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStockTransfers::route('/'),
            'create' => Pages\CreateStockTransfer::route('/create'),
            // 'edit' => Pages\EditStockTransfer::route('/{record}/edit'),
            'view' => ViewStockTransfer::route('/{record}'),

        ];
    }
}
