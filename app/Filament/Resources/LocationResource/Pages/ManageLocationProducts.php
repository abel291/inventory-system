<?php

namespace App\Filament\Resources\LocationResource\Pages;

use App\Filament\Resources\LocationResource;
use App\Filament\Resources\StockAdjustmentResource;
use App\Models\Product;
use App\Models\Stock;
use Filament\Actions;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Pages\ManageRelatedRecords;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ManageLocationProducts extends ManageRelatedRecords
{
    protected static string $resource = LocationResource::class;

    protected static string $relationship = 'products';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string
    {
        return 'Productos';
    }
    public function getTitle(): string
    {
        return $this->record->nameType;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('product_id')
                    ->label('Producto')
                    ->live()
                    ->placeholder('Codigo de barra o nombre del producto')
                    ->getSearchResultsUsing(function (string $search) {

                        return Product::select('name', 'id', 'barcode')->whereAny(
                            ['name', 'barcode'],
                            'like',
                            "%{$search}%"

                        )
                            ->limit(50)->get()->pluck('nameBarcode', 'id')->toArray();
                    })
                    // ->options(Product::all()->pluck('nameBarcode', 'id'))
                    ->getOptionLabelFromRecordUsing(fn (Product $record) => "{$record->nameBarcode}")

                    ->afterStateUpdated(function (Get $get, Set $set) {

                        $stock = $this->record->products()->where('product_id', $get('product_id'))->first();

                        if ($stock) {
                            $set('initial_stock', $stock->pivot->remaining);
                        } else {
                            $set('initial_stock', 0);
                        }
                        $set('quantity', 0);
                        $set('final_stock', 0);
                    })
                    ->searchable(['name', 'barcode'])
                    ->native(false)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('cost')
                    ->label('Costo')
                    ->numeric()
                    ->minValue(0)
                    ->prefix('$')
                    ->required(),
                Forms\Components\TextInput::make('price')
                    ->required()
                    ->numeric()
                    ->label('Precio')
                    ->prefix('$')->columnSpan(2),

                Forms\Components\TextInput::make('initial_stock')
                    ->label('Existencia Actual')
                    ->disabled(),

                Forms\Components\TextInput::make('quantity')
                    ->label('Cantidad Agregada')
                    ->live(debounce: 200)
                    ->minValue(1)
                    ->afterStateUpdated(
                        function (Get $get, Set $set) {

                            if ($get('quantity') < 0) {
                                $set('quantity', '');
                                return;
                            }

                            if ($get('initial_stock')) {
                                $set('final_stock', $get('initial_stock') +  (int) $get('quantity'));
                            } else {
                                $set('final_stock', 0);
                            }
                        }
                    )
                    ->disabled(function (Get $get, Set $set) {
                        return !$get('product_id');
                    })
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('final_stock')
                    ->label('Existencia Final')
                    ->disabled(),

            ])

            ->columns(3);
    }

    public function table(Table $table): Table
    {
        return $table

            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\ImageColumn::make('img')->label('Imagen'),
                Tables\Columns\TextColumn::make('nameBarcode')
                    ->wrap()
                    ->label('Codigo de barra - Nombre')
                    ->searchable(['name', 'barcode']),

                Tables\Columns\TextColumn::make('price')->money('COP', locale: 'ES')->label('Precio'),
                Tables\Columns\TextColumn::make('pivot.remaining')->label('Existencia en bodega'),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->label('Categoria')
                    ->relationship('category', 'name'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()->label(function () {
                    return "Agregar mercancia a " . $this->record->nameType;
                })
                    ->modalHeading(function () {
                        return "Agregar Producto a " . $this->record->name;
                    })
                    ->using(function (array $data, string $model): Model {

                        return Stock::create([
                            'product_id' => $data['product_id'],
                            'location_id' => $this->record->id,
                            'quantity' => $data['quantity'],
                            'price' => $data['price'],
                            'cost' => $data['cost'],
                        ]);
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('products')
                    ->label('Ver Movimientos')
                    ->url(fn (Product $record): string => StockAdjustmentResource::getUrl('index', [])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->heading('Productos en ' . $this->record->type->getLabel() . " (" . $this->record->products->count() . ")")
            ->description('El ingreso de mercancia se gestiona desde la seccion "Mercancias"')
            ->emptyStateDescription('El ingreso de mercancia se gestiona desde la seccion "Mercancias"')
            ->searchPlaceholder('Codigo de barra o nombre del producto')

            ->defaultSort('id', 'desc');
    }
}
