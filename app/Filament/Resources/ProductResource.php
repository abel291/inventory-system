<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages\ManageCategories;
use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Filament\Resources\ProductResource\Widgets\ProductStats;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Infolists\Infolist;
use Filament\Pages\SubNavigationPosition;

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
					->label('Nombre del  producto')
					->required(),
				Forms\Components\Select::make('category_id')
					->relationship(name: 'category', titleAttribute: 'name'),
				Forms\Components\Textarea::make('description_min')
					->label('Descripcion pequeña')
					->columnSpanFull(),
				Forms\Components\TextInput::make('cost')
					->label('Costo')
					->numeric()
					->prefix('$'),
				Forms\Components\TextInput::make('price')
					->required()
					->numeric()
					->label('Precio final')
					->minValue(0)
					->default(0)
					->prefix('$'),

				Forms\Components\FileUpload::make('img')
					->image()
					->directory('products')
					->maxSize(1024)
					->label('Imagen'),
				// Forms\Components\TextInput::make('discount')
				//     ->numeric(),
				// Forms\Components\TextInput::make('price_discount')
				//     ->numeric(),
				// Forms\Components\TextInput::make('stock')
				//     ->numeric(),
				// Forms\Components\TextInput::make('max_quantity')
				//     ->required()
				//     ->numeric(),
				// Forms\Components\TextInput::make('min_quantity')
				//     ->required()
				//     ->numeric(),
				Forms\Components\Toggle::make('active')
					->required(),

			]);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\ImageColumn::make('img'),
				Tables\Columns\TextColumn::make('name')->label('Nombre')->wrap()
					->description(fn (Product $record): string => '#' . $record->barcode)
					->searchable(),

				Tables\Columns\TextColumn::make('locations.name')->badge()->label('Ubicaciones'),

				Tables\Columns\TextColumn::make('price')
					->money('COP', locale: 'ES')->label('Precio')
					->sortable(),

				// Tables\Columns\TextColumn::make('stock')
				//     ->label('Cantidad')
				//     ->numeric()
				//     ->sortable(),

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
				Tables\Actions\ViewAction::make()->icon(false)->label('Ver stock'),
				Tables\Actions\EditAction::make()->icon(null)->icon(false),
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
			// 'create' => Pages\CreateProduct::route('/create'),
			// 'edit' => Pages\EditProduct::route('/{record}/edit'),
		];
	}
}
