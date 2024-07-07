<?php

namespace App\Filament\Resources;

use App\Enums\LocationTypeEnum;
use App\Enums\StockAdjustmentTypeEnum;
use App\Filament\Resources\StockAdjustmentResource\Pages\CreateStockAdjustment;
use App\Filament\Resources\StockAdjustmentResource\Pages\ListStockAdjustments;
use App\Filament\Resources\StockAdjustmentResource\Pages\ManageStockAdjustments;
use App\Models\Location;
use App\Models\Stock;
use App\Models\StockAdjustment;
use Closure;
use Filament\Forms;
use Livewire\Component as Livewire;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;


class StockAdjustmentResource extends Resource
{
	protected static ?string $model = StockAdjustment::class;

	protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';

	public static ?string $label = 'Ajuste de stock';

	protected static ?string $pluralModelLabel  = 'Ajuste de stock';
	protected static ?string $navigationGroup  = 'Inventario';

	public static function form(Form $form): Form
	{
		return $form
			->schema([
				Forms\Components\Select::make('location_id')
					->label('Bodega')
					->live()
					->afterStateUpdated(function (Set $set) {
						$set('product_id', '');
					})
					->options(Location::where('type', LocationTypeEnum::WAREHOUSE)->get()->pluck('name', 'id')),

				Forms\Components\Select::make('product_id')
					->label('Producto')
					->options(function (Forms\Get $get) {
						return Stock::with('product:id,name')->where('location_id', $get('location_id'))->get()->pluck('product.name', 'product.id');
					})
					->live()
					->afterStateUpdated(function (Get $get, Set $set) {
						$stock = Stock::where([
							['location_id', $get('location_id')],
							['product_id', $get('product_id')],
						])->first();
						if ($stock) {

							$set('initial_stock', $stock->stock);
						} else {
							$set('initial_stock', 0);
						}
						$set('initial_stock', 10);
					})
					->searchable()
					->native(false),

				Forms\Components\TextInput::make('initial_stock')
					->label('Stock inicial')
					->hidden(fn (Get $get): bool => !$get('product_id'))
					->disabled(),

				Forms\Components\Radio::make('type')
					->label('Tipo de operacion')

					->live()
					->afterStateUpdated(fn (Get $get, Set $set) => self::handleChangeStock($get, $set))
					->options(StockAdjustmentTypeEnum::class)
					->hidden(fn (Get $get): bool => !$get('product_id')),

				Forms\Components\TextInput::make('adjustment')
					->label('Ajuste')
					->numeric()
					->minValue(function (Get $get, Set $set) {
						return $get('initial_stock');
					})
					->live()
					->afterStateUpdated(fn (Get $get, Set $set) => self::handleChangeStock($get, $set))
					->hidden(fn (Get $get): bool => !$get('product_id')),

				Forms\Components\TextInput::make('final_stock')
					->label('Stock final')
					->disabled()
					->hidden(fn (Get $get): bool => !$get('product_id'))
			]);

		//             initial_stock
		// adjustment
		// final_stock
	}
	public static function handleChangeStock($get, $set)
	{

		$final_stock = 0;
		if ($get('type') == StockAdjustmentTypeEnum::INCREASE->value) {
			$final_stock = $get('initial_stock') + $get('adjustment');
		} elseif ($get('type') == StockAdjustmentTypeEnum::DECREASE->value) {
			$final_stock = $get('initial_stock') - $get('adjustment');
		}

		$set('final_stock', $final_stock);
	}

	public static function table(Table $table): Table
	{
		return $table
			->columns([
				Tables\Columns\TextColumn::make('stock.product.name')
					->wrap()
					->description(fn (StockAdjustment $record): string => $record->user->name)
					->label('Producto - Responsable'),
				Tables\Columns\TextColumn::make('stock.location.name')->badge()->label('Bodega'),
				Tables\Columns\TextColumn::make('type')->badge()->label('Tipo de ajsute'),
				Tables\Columns\TextColumn::make('initial_stock')->numeric(decimalPlaces: 0)->label('Stock inicial'),
				Tables\Columns\TextColumn::make('adjustment')->numeric(decimalPlaces: 0)->label('Ajuste'),
				Tables\Columns\TextColumn::make('final_stock')->numeric(decimalPlaces: 0)->label('Stock final'),
				Tables\Columns\TextColumn::make('updated_at')
					->label('Fecha de creacion')
					->since(),

			])
			->filters([
				//
			])
			->actions([
				// Tables\Actions\EditAction::make()->icon(null),
				// Tables\Actions\DeleteAction::make(),
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
			'index' => ListStockAdjustments::route('/'),
			'create' => CreateStockAdjustment::route('/create'),
		];
	}
}
