<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;


class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static ?string $label = 'Mercancia';

    protected static ?string $pluralModelLabel  = 'Mercancias';
    protected static ?string $navigationGroup  = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('location_id')
                    ->relationship(name: 'location', titleAttribute: 'name')
                    ->getOptionLabelFromRecordUsing(fn (Location $record) => $record->type->getLabel() . " | " . $record->name)
                    ->label('Ubicacion')
                    ->native(false)
                    ->required(),

                Repeater::make('products')
                    ->label('Productos')
                    ->schema([
                        Forms\Components\Select::make('product_id')
                            ->LABEL('Producto')
                            ->placeholder('Escriba el codigo de barra o nombre del producto')
                            ->relationship(
                                name: 'product',
                                titleAttribute: 'name'
                            )
                            ->getOptionLabelFromRecordUsing(fn (Product $record) => "{$record->barcode} - {$record->name}")
                            ->searchable(['name', 'barcode'])
                        // ->getSearchResultsUsing(
                        //     fn (string $search): array =>
                        //     Product::select('name', 'barcode', 'id')
                        //         ->whereAny(['name', 'barcode'], 'like', "%{$search}%")
                        //         ->limit(50)
                        //         ->pluck('name', 'id')
                        //         ->toArray()
                        // )
                        // ->getOptionLabelFromRecordUsing(fn (Product $record) => "{$record->barcode} {$record->name}")

                        // ->searchable()
                        // ->label('Productos')
                        // ->required()
                        ,
                        Forms\Components\TextInput::make('stock')
                            ->required()
                            ->numeric(),
                        // Forms\Components\TextInput::make('security_stock')
                        //     ->required()
                        //     ->numeric(),
                    ])->columnSpanFull()
            ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.name')->label('Producto')
                    ->wrap()
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location.name')->label('Ubicacion')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->numeric()
                // ->sortable()
                ,
                Tables\Columns\TextColumn::make('security_stock')
                    ->numeric()
                // ->sortable()
                ,

                // ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('location')
                    ->label('Ubicacion')
                    ->relationship('location', 'name'),
                SelectFilter::make('product')
                    ->label('Producto')
                    ->relationship('product', 'name')
                    ->searchable()
            ])
            ->filtersTriggerAction(
                fn (Action $action) => $action
                    ->button()
                    ->label('Filter'),
            )
            ->actions([
                Tables\Actions\EditAction::make()->icon(null)->icon(false),
                Tables\Actions\DeleteAction::make()->icon(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }
}
