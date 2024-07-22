<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use BladeUI\Icons\Components\Icon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Livewire\Attributes\Layout;

class StockResource extends Resource
{
    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';

    public static ?string $label = 'Mercancia';
    protected static ?string $pluralModelLabel = 'Inventario';
    protected static ?string $navigationGroup = 'Inventario';

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
                    ->searchable(['barcode', 'name'])
                    ->description(fn (Stock $record): string => $record->product->barcode)
                    ->wrap()->label('Codigo - Nombre'),
                Tables\Columns\TextColumn::make('location.name')->label('Ubicacion')->badge(),
                Tables\Columns\TextColumn::make('product.price')->label('Precio')->numeric()->prefix('$'),
                // Tables\Columns\TextColumn::make('quantity')->label('Existencia'),
                Tables\Columns\TextColumn::make('quantity')
                    ->sortable()
                    ->label('Existencia'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Ultima Modificacion')
                    ->since(),

            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('location', 'product.category');
            })
            ->filters([

                SelectFilter::make('location_id')
                    ->options(Location::all()->pluck('name', 'id'))
                    ->columnSpan(1)
                    ->preload()->label('Ubicacion')
            ], layout: FiltersLayout::Dropdown)
            ->striped()
            ->searchPlaceholder('Codigo o nombre del producto')
            ->defaultSort('id', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageStocks::route('/'),
        ];
    }
}
