<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\Widgets\ProductStats;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;

use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;


class ProductResource extends Resource
{
    protected static ?string $model = Product::class;
    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    public static ?string $label = 'Producto';
    protected static ?string $pluralModelLabel  = 'Productos';
    protected static ?string $navigationGroup  = 'Inventario';

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'barcode', 'reference'];
    }
    protected static ?string $recordTitleAttribute = 'nameBarcode';
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nombre del  producto')
                    ->required()
                    ->columnSpan(2),
                Forms\Components\Select::make('category_id')
                    ->relationship(name: 'category', titleAttribute: 'name'),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->label('Precio')
                    ->prefix('$'),


                Forms\Components\TextInput::make('barcode')
                    ->required()
                    ->label('Codigo de barra (ISBN, UPC, GTIN, etc.)'),
                Forms\Components\TextInput::make('reference')
                    ->required()
                    ->label('Referencia'),
                Forms\Components\TextInput::make('security_stock')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->helperText('El stock de seguridad es el stock límite de tus productos que te avisa si el stock del producto pronto se agotará.')
                    ->label('Stock de seguridad'),
                Forms\Components\TextInput::make('max_quantity')
                    ->label('Cantidad maxima para vender')
                    ->helperText('Cantidad maxima permitida por cada venta')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('min_quantity')
                    ->label('Cantidad minima para vender')
                    ->helperText('Cantidad minima permitida por cada venta')
                    ->required()
                    ->numeric(),
                Forms\Components\Textarea::make('description_min')
                    ->label('Descripcion pequeña')
                    ->rows(4)
                    ->columnSpan(2),

                Forms\Components\FileUpload::make('img')
                    ->image()
                    ->maxSize(1024)
                    ->label('Imagen'),

                Forms\Components\Toggle::make('active')
                    ->default(1)
                    ->required()
                    ->translateLabel(),

            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->striped()
            ->columns([
                Tables\Columns\ImageColumn::make('img')->label('Imagen')->height(56),
                Tables\Columns\TextColumn::make('name')->translateLabel()->wrap()
                    ->description(fn (Product $record): string => $record->barcode),

                Tables\Columns\TextColumn::make('locations')
                    ->formatStateUsing(fn ($state): string => $state->nameType)
                    ->badge()->label('Ubicaciones'),

                Tables\Columns\TextColumn::make('price')
                    ->money('COP', locale: 'ES')->label('Precio')
                    ->sortable(),
                Tables\Columns\IconColumn::make('active')
                    ->translateLabel()
                    ->boolean(),

            ])
            ->modifyQueryUsing(function (Builder $query) {
                return $query->with('locations');
                // ->where('author_id', auth()->id());
            })
            ->filters(self::filterProduct())
            ->actions([
                // Tables\Actions\ViewAction::make()->icon(false)->label('Ver stock'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function filterProduct()
    {
        return [
            SelectFilter::make('product')
                ->options(Product::all()->pluck('nameBarcodePrice', 'id'))
                ->optionsLimit(12)
                ->searchable()
                ->attribute('id')
                ->label('Producto')
                ->columnSpan(2),
            SelectFilter::make('category')
                ->relationship('category', 'name')
                ->preload()->label('Categoria'),

            SelectFilter::make('locations')
                ->relationship('locations', 'name')
                ->preload()->label('Ubicacion')
        ];
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
