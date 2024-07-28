<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockTransfer;
use App\Models\User;
use BladeUI\Icons\Components\Icon;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use phpDocumentor\Reflection\DocBlock\Tags\See;
use Livewire\Component as Livewire;

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
				Tables\Columns\ImageColumn::make('product.img')->label('Imagen'),
				Tables\Columns\TextColumn::make('product.name')->label('Codigo - Nombre')
					->searchable(['barcode', 'name'])
					->description(fn (Stock $record): string => $record->product->barcode)
					->wrap(),
				Tables\Columns\TextColumn::make('location.nameType')->label('Ubicacion')
					->badge(),
				Tables\Columns\TextColumn::make('product.price')->label('Precio')
					->numeric()
					->prefix('$'),
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
			])
			->actions([

				Action::make('updateAuthor')
					->visible(fn ($record) => ($record->quantity))
					->label('Trasladar mercancia')
					->fillForm(fn (Stock $record): array => [

						'product_id' => $record->product_id,
						'product_name' => $record->product->nameBarcodePrice,

						'location_from_id' => $record->location_id,
						'location_from_name' => "{$record->location->nameType} - Stock:{$record->quantity}",

						'user_request_id' => auth()->id(),
						'user_request_name' => auth()->user()->name,

					])
					->form([
						Grid::make(5)
							->schema([
								Hidden::make('product_id'),
								Hidden::make('location_from_id'),

								TextInput::make('product_name')->label('Producto')
									->disabled()
									->columnSpan(3),

								TextInput::make('user_request_name')->label('Solicitante')
									->disabled()->columnSpan(2),

								TextInput::make('location_from_name')->label('Origen')
									->disabled()->columnSpan(2),

								Select::make('location_to_id')->label('Destino')
									->columnSpan(2)
									->required()
									->options(
										function (Get $get) {
											$options = Location::active()
												->whereNot('id', $get('location_from_id'))
												->get()->pluck('nameType', 'id');

											return $options;
										}
									),
								TextInput::make('quantity')->label('Cantidad a trasladar')
									->required()
									->placeholder(fn (Stock $stock) => ("Maximo: {$stock->quantity}"))
									->numeric()
									->minValue(1)
									->maxValue(fn (Stock $stock) => ($stock->quantity)),
								Textarea::make('note')->label('Nota')->columnSpanFull()->placeholder('Alguna informacion importante')

							])
					])

					->action(function (array $data): void {

						DB::transaction(function () use ($data) {
							$stockTransfer = StockTransfer::create([
								'user_request_id' => auth()->id(),
								'location_from_id' => $data['location_from_id'],
								'location_to_id' => $data['location_to_id'],
								'note' => $data['note']
							]);
							$stockTransfer->products()
								->attach($data['product_id'], ['quantity' =>  $data['quantity']]);
							// ->create([
							//     'product_id' => $data['product_id'],
							//     'quantity' => $data['quantity']
							// ]);
							redirect()->route('filament.admin.resources.stock-transfers.view', [$stockTransfer->id]);
						});
					})

			])
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
