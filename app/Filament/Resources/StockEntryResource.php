<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockEntryResource\Pages\CreateStockEntry;
use App\Filament\Resources\StockEntryResource\Pages\ListStockEntry;
use App\Filament\Resources\StockEntryResource\Pages\ManageStockEntryProducts;
use App\Filament\Resources\StockResource\Pages;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockEntry;
use Filament\Forms;

use Livewire\Component as Livewire;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


use Filament\Tables\Enums\FiltersLayout;

class StockEntryResource extends Resource
{
    protected static ?string $model = StockEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    public static ?string $label = 'Entrada mercancia';
    protected static ?string $pluralModelLabel = 'Entrada mercancia';
    protected static ?string $navigationGroup = 'Inventario';

    public static function form(Form $form): Form
    {

        return $form
            ->schema([

                Forms\Components\Select::make('location_id')
                    ->label('Ubicacion')
                    ->live()
                    ->required()
                    ->afterStateUpdated(function ($set) {
                        $set('stockEntryProducts', [
                            [
                                'product_id' => null,
                                'stock' => null,
                                'quantity' => null,
                            ]
                        ]);
                    })
                    ->options(Location::all()->pluck('nameType', 'id')),

                Forms\Components\Repeater::make('stockEntryProducts')
                    ->hidden(fn (Get $get): bool => !$get('location_id'))
                    ->relationship()
                    ->required()
                    ->label('Productos')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->label('Nombre del producto')
                            ->live()
                            ->placeholder('Codigo de barra o nombre del producto')
                            ->relationship(
                                name: 'product',
                                titleAttribute: 'name',
                                modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                    ->with(['locations' => function ($query2) use ($get) {
                                        $query2->where('locations.id', $get('../../location_id'));
                                    }])
                                    ->whereRelation('locations', 'locations.id', $get('../../location_id')),
                            )
                            ->optionsLimit(5)
                            ->getOptionLabelFromRecordUsing(function (Product $record) {

                                return "{$record->nameBarcodePrice}";
                            })
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            ->searchable(['barcode', 'name', 'reference'])
                            // ->options(function (Get $get, Livewire $livewire) {

                            //     return Product::whereRelation('locations', 'locations.id', $get('../../location_id'))->get()
                            //         ->pluck('nameBarcodePrice', 'id');
                            // })
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {

                                $stock = Stock::where([
                                    ['product_id', $state],
                                    ['location_id', $get('../../location_id')],
                                ])->first();

                                $set('stock', $stock->quantity);
                                $set('quantity', null);
                            })
                            ->native(false)
                            ->required()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('stock')
                            ->label('Existencia')
                            ->disabled()
                            ->numeric(),

                        Forms\Components\TextInput::make('quantity')
                            ->label('Cantidad')
                            ->maxValue(fn (Get $get) => $get('stock'))
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('cost')
                            ->label('Costo')
                            ->required()
                            ->suffix('COP')
                            ->numeric()
                            ->minValue(0)
                    ])
                    ->addActionLabel('Añadir otro producto')
                    ->default([
                        [
                            'product_id' => null,
                            'stock' => null,
                            'quantity' => null,
                        ]
                    ])

                    ->columnSpanFull()
                    ->columns(3),
            ])->columns(3);
    }



    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')->label('Responsable'),
                Tables\Columns\TextColumn::make('location.nameType')->label('Ubicacion')->badge(),
                Tables\Columns\TextColumn::make('products_count')->counts('products')->label('Productos'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Creado')
                    ->searchable()
                    ->dateTime()

            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('location', 'products', 'user');
            })
            ->filters(self::filterProduct(), layout: FiltersLayout::Dropdown)

            ->actions([
                Tables\Actions\ViewAction::make()->icon(false)
                    ->modalHeading('Informacion de los productos')
                    ->infolist([
                        TextEntry::make('user.name')->label('Responsable'),
                        ViewEntry::make('products')->view('filament.infolists.entries.stock-entry-product-list')
                    ])->label('Ver productos'),
                // Tables\Actions\Action::make('products')
                //     ->label('Ver productos')
                //     ->url(fn (StockEntry $record): string => route('filament.admin.resources.stock-entries.products', $record)),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchPlaceholder('Codigo de barra o nombre del producto')
            // ->description('Desde aqui puede ver el historial de ingreso de mercancia a las diferentes Ubicaciones')
            ->defaultSort('id', 'desc');
    }



    public static function filterProduct()
    {
        return [
            // SelectFilter::make('products.id')
            //     ->options(Product::all()->pluck('nameBarcodePrice', 'id'))
            //     ->optionsLimit(12)
            //     ->label('Producto')
            //     ->searchable()
            //     ->columnSpan(3),
            // SelectFilter::make('products.category')
            //     ->relationship('products.category', 'name')
            //     ->preload()->label('Categoria'),

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
            'products' => ManageStockEntryProducts::route('/{record}/products'),
        ];
    }
}
