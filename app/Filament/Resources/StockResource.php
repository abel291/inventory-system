<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static ?string $label = 'Mercancia';
    protected static ?string $pluralModelLabel  = 'Mercancia';
    protected static ?string $navigationGroup  = 'Inventario';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
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
                Tables\Columns\TextColumn::make('cost')->label('Costo')->numeric()->prefix('$'),
                Tables\Columns\TextColumn::make('product.price')->label('Precio')->numeric()->prefix('$'),
                // Tables\Columns\TextColumn::make('quantity')->label('Existencia'),
                Tables\Columns\TextColumn::make('remaining')
                    ->sortable()
                    ->label('Existencia'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ultima Modificacion')
                    ->SINCE()

            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('location', 'product.category')
                    ->where('type', 'total');
            })
            ->filters([
                SelectFilter::make('product_id')
                    ->options(Product::all()->pluck('nameBarcode', 'id'))
                    ->optionsLimit(12)
                    ->label('Producto')
                    ->searchable()
                    ->columnSpan(3),
                SelectFilter::make('location_id')
                    ->options(Location::all()->pluck('name', 'id'))
                    ->columnSpan(2)
                    ->preload()->label('Ubicacion')
            ])

            ->searchPlaceholder('Codigo de barra o nombre del producto')
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStocks::route('/'),
        ];
    }
}
