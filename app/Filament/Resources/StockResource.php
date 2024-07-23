<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StockResource\Pages;
use App\Filament\Resources\StockResource\RelationManagers;
use App\Models\Location;
use App\Models\Product;
use App\Models\Stock;
use App\Models\User;
use BladeUI\Icons\Components\Icon;

use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
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
                Tables\Columns\ImageColumn::make('product.img')->label('Imagen'),
                Tables\Columns\TextColumn::make('product.name')
                    ->searchable(['barcode', 'name'])
                    ->description(fn(Stock $record): string => $record->product->barcode)
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
            ])
            ->actions([

                Action::make('updateAuthor')
                    ->label('Trasladar mercancia')
                    ->fillForm(fn(Stock $record): array => [
                        'product_name' => $record->product->nameBarcode,
                        'product_id' => $record->product_id,
                        'location_from_id' => $record->location_id,
                        'location_from_name' => $record->location->name,
                    ])
                    ->form([
                        Grid::make(3)
                            ->schema([
                                TextInput::make('product_name')->label('Producto')->disabled()->columnSpanFull(),
                                TextInput::make('product_id')->hidden(),
                                TextInput::make('location_from_name')
                                    ->disabled()
                                    ->label('origen')
                                ,

                                TextInput::make('location_from_id')->hidden(),

                                Select::make('location_to_id')
                                    ->label('Destino')
                                    ->options(
                                        function (Get $get) {

                                            $options = Stock::with('location')
                                                ->whereNot('location_id', $get('location_from_id'))
                                                ->where('product_id', $get('product_id'))
                                                ->get()->mapWithKeys(function ($item) {
                                                    return [
                                                        $item->location_id => "{$item->location->name} - stock {$item->quantity}"
                                                    ];
                                                });

                                            return $options;
                                        }
                                    )
                                    ->preload(),
                                TextInput::make('quantity'),

                            ])
                    ])

                    ->action(function (array $data, $record): void {
                        dd($record);
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
