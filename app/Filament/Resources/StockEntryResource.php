<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockEntryResource\Pages\CreateStockEntry;
use App\Filament\Resources\StockEntryResource\Pages\ListStockEntry;
use App\Filament\Resources\StockResource\Pages;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class StockEntryResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    public static ?string $label = 'Entrada mercancia';
    protected static ?string $pluralModelLabel  = 'Entrada mercancia';
    protected static ?string $navigationGroup  = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->live()
                    ->placeholder('Codigo de barra o nombre del producto')
                    ->options(Product::all()->pluck('nameBarcodePrice', 'id'))
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::getStockProduct($get, $set);
                    })
                    ->searchable()
                    ->native(false)
                    ->required()
                    ->columnSpan(2),

                Forms\Components\Select::make('location_id')
                    ->label('Ubicacion')
                    ->live()
                    ->required()
                    ->options(Location::all()->pluck('name', 'id'))
                    ->afterStateUpdated(function (Get $get, Set $set) {
                        self::getStockProduct($get, $set);
                    }),

                Forms\Components\TextInput::make('cost')
                    ->label('Costo')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$')
                    ->required(),
                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad Agregada')
                    ->live(debounce: 300)
                    ->minValue(1)

                    ->disabled(function (Get $get) {
                        return !$get('location_id') || !$get('product_id');
                    })
                    ->required()
                    ->numeric(),

                Forms\Components\TextInput::make('actual_stock')
                    ->label(function (string $operation) {


                        return 'Existencia Actual ';
                        // .
                        // match ($operation) {
                        //     'create' => '(excluyendo esta entrada)',
                        //     'edit' => '(incluyendo esta entrada)',
                        //     default => '',
                        // };
                    })
                    ->disabled(),
            ])->columns(3);
    }


    public static function getStockProduct(Get $get, Set $set)
    {

        if ($get('location_id') && $get('product_id')) {

            $stock = Stock::where([
                ['product_id', $get('product_id')],
                ['location_id', $get('location_id')],
                ['type', 'total']
            ])->first();

            if ($stock) {
                $set('actual_stock', $stock->remaining);
            } else {
                $set('actual_stock', 0);
            }
            $set('quantity', 0);
        }
    }
    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('product.img')->label('Imagen'),
                Tables\Columns\TextColumn::make('product.name')
                    ->description(fn (Stock $record): string => $record->product->barcode)
                    ->wrap()->label('Codigo - Nombre'),
                Tables\Columns\TextColumn::make('location.name')->label('Ubicacion')->badge(),
                Tables\Columns\TextColumn::make('user.name')->label('Responsable'),
                Tables\Columns\TextColumn::make('cost')->numeric()->prefix('$'),
                Tables\Columns\TextColumn::make('quantity')->label('Entrada'),
                Tables\Columns\TextColumn::make('remaining')
                    ->sortable()
                    ->label('Existencia'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Creado')
                    ->dateTime()

            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('location', 'product.category', 'user')->where('type', 'stock');
            })
            ->filters(self::filterProduct())

            ->actions([
                // Tables\Actions\EditAction::make()->icon(false),
                Tables\Actions\DeleteAction::make()->icon(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchPlaceholder('Codigo de barra o nombre del producto')
            ->description('Desde aqui puede ver el historial de ingreso de mercancia a las diferentes Ubicaciones')
            ->defaultSort('id', 'desc');
    }

    public static function filterProduct()
    {
        return [
            SelectFilter::make('product_id')
                ->options(Product::all()->pluck('nameBarcodePrice', 'id'))
                ->optionsLimit(12)
                ->label('Producto')
                ->searchable()
                ->columnSpan(3),
            SelectFilter::make('product.category')
                ->relationship('product.category', 'name')
                ->preload()->label('Categoria'),

            SelectFilter::make('location_id')
                ->options(Location::all()->pluck('name', 'id'))
                ->preload()->label('Ubicacion')
        ];
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
            'index' => ListStockEntry::route('/'),
            'create' => CreateStockEntry::route('/create'),
            // 'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
