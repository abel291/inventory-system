<?php

namespace App\Filament\Resources;

use App\Enums\StockStatuEnum;
use App\Filament\Resources\StockEntryResource\Pages\CreateStockEntry;
use App\Filament\Resources\StockEntryResource\Pages\ListStockEntry;
use App\Filament\Resources\StockEntryResource\Pages\ManageStockEntryProducts;
use App\Filament\Resources\StockEntryResource\Pages\ViewStockEntry;
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
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Split;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


use Filament\Tables\Enums\FiltersLayout;
use Illuminate\Support\Collection;

class StockEntryResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = StockEntry::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-end-on-rectangle';

    public static ?string $label = 'Entrada mercancia';
    protected static ?string $pluralModelLabel = 'Entrada mercancia';
    protected static ?string $navigationGroup = 'Inventario';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'change_status'
        ];
    }

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
                Forms\Components\Grid::make()->columns(2),

                Forms\Components\Repeater::make('stockEntryProducts')
                    ->hidden(fn(Get $get): bool => !$get('location_id'))
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
                                // modifyQueryUsing: fn (Builder $query, Get $get) => $query
                                //     ->with('locations')
                                //     ->whereHas('locations', function (Builder $query) use ($get) {
                                //         $query->where('location_id', $get('../../location_id'));
                                //     }),
                            )
                            ->searchable(['name', 'barcode'])
                            ->getOptionLabelFromRecordUsing(function (Product $record) {
                                return "{$record->nameBarcodePrice}";
                            })
                            ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                            // ->preload()
                            // ->options(Product::select('id', 'name', 'barcode', 'price')->get()->pluck('nameBarcodePrice', 'id'))

                            // ->searchable()
                            ->afterStateUpdated(function (Get $get, Set $set, $state) {
                                $stock = Stock::where([
                                    ['product_id', $state],
                                    ['location_id', $get('../../location_id')],
                                ])->first();

                                $set('stock', $stock ? $stock->quantity : 0);
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
                            ->default(1)
                            ->required()
                            ->numeric()
                            ->minValue(1),
                        Forms\Components\TextInput::make('cost')
                            ->label('Costo')
                            ->required()
                            ->prefix('$')
                            ->numeric()
                            ->required()
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
                Tables\Columns\TextColumn::make('status')->label('Estado')->badge(),
                Tables\Columns\TextColumn::make('products_count')->counts('products')->label('Productos'),
                Tables\Columns\TextColumn::make('products_sum_stock_entry_productcost')->sum('products', 'stock_entry_product.cost')
                    ->money()
                    ->label('Coste Total'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Creado')
                    ->searchable()
                    ->dateTime()

            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('location', 'products', 'user');
            })
            ->filters(
                [
                    SelectFilter::make('location_id')
                        ->options(Location::all()->pluck('name', 'id'))
                        ->preload()->label('Ubicacion')
                ]
            )

            ->actions([
                Tables\Actions\ViewAction::make()->label('Ver productos'),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->searchable()

            ->searchPlaceholder('Codigo de barra o nombre del producto');
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


        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockEntry::route('/'),
            'create' => CreateStockEntry::route('/create'),
            'view' => ViewStockEntry::route('/{record}'),
            'products' => ManageStockEntryProducts::route('/{record}/products'),
        ];
    }
}
