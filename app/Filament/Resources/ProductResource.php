<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages\ManageCategories;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\Widgets\ProductStats;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    public static ?string $label = 'Producto';
    protected static ?string $pluralModelLabel  = 'Productos';
    protected static ?string $navigationGroup  = 'Inventario';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),
                Forms\Components\TextInput::make('slug')
                    ->required(),
                Forms\Components\Textarea::make('description_min')
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('img'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->prefix('$'),
                Forms\Components\TextInput::make('cost')
                    ->numeric()
                    ->prefix('$'),
                Forms\Components\TextInput::make('discount')
                    ->numeric(),
                Forms\Components\TextInput::make('price_discount')
                    ->numeric(),
                Forms\Components\TextInput::make('stock')
                    ->numeric(),
                Forms\Components\TextInput::make('max_quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('min_quantity')
                    ->required()
                    ->numeric(),
                Forms\Components\Toggle::make('active')
                    ->required(),
                Forms\Components\TextInput::make('category_id')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('img'),
                Tables\Columns\TextColumn::make('name')->label('Nombre')->wrap()
                    ->description(fn (Product $record): string => '#' . $record->code)
                    ->searchable(),

                Tables\Columns\TextColumn::make('warehouses.name')->badge()->label('Bodegas'),

                Tables\Columns\TextColumn::make('price')
                    ->money('COP', locale: 'ES')->label('Precio')
                    ->sortable(),
                Tables\Columns\TextColumn::make('stock')
                    ->label('Cantidad')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->label('Activo')
                    ->boolean(),

            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name')
                    //  ->searchable()
                    ->preload()->label('Categoria')
            ])
            ->actions([
                Tables\Actions\EditAction::make()->icon(false),
                Tables\Actions\DeleteAction::make()->icon(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getWidgets(): array
    {
        return [
            ProductStats::class,
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
