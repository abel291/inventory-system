<?php

namespace App\Filament\Resources\SaleResource\Section;

use App\Forms\Components\SaleProductList;
use App\Models\Product;
use App\Models\Stock;
use App\Services\SaleService;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Support\RawJs;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class SaleFormSection
{
    public static function products(): array
    {
        return [
            Grid::make(4)->schema([
                TextInput::make('barcode')->label('Buscar por codigo')
                    ->placeholder('Codigo de barra')
                    ->autocomplete(false)
                    ->live()
                    ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                        self::searchProduct('barcode', $state, $get, $set);
                    })
                    ->extraAttributes(['onkeydown' => "return event.key != 'Enter';"])
                    ->dehydrated(false),
                Select::make('stock_id')->label("Buscar por nombre")
                    ->live()
                    ->placeholder('Escriba el nombre del producto')
                    ->columnSpan(2)
                    ->searchable()
                    ->preload()
                    ->options(function (Get $get) {
                        return Stock::with('product:id,name,price,barcode,reference')
                            ->where('location_id', $get('location_id'))
                            ->where('quantity', '>', 0)
                            ->get()->pluck('product.nameBarcodePrice', 'id');
                    })
                    ->afterStateUpdated(function (?string $state, Get $get, Set $set) {
                        self::searchProduct('stock_id', $state, $get, $set);
                    })
                    ->dehydrated(false),
            ]),

            Repeater::make('saleProducts')
                ->relationship()
                ->required()
                ->label('')
                ->default([])
                ->columnSpanFull()
                ->columns(4)
                ->addable(false)
                ->afterStateUpdated(function (Get $get, Set $set) {
                    self::updateTotals($get, $set);
                })
                ->schema([
                    Hidden::make('product_id'),
                    TextInput::make('product_name')
                        ->label('')
                        ->disabled()->columnSpan(4),

                    TextInput::make('stock')
                        ->label('Existencia')
                        ->disabled(),

                    TextInput::make('price')
                        ->label('Precio')
                        ->prefix('$')
                        ->disabled()
                        ->stripCharacters('.')
                        ->dehydrated(),

                    TextInput::make('quantity')
                        ->label('Cantidad')
                        ->default(1)
                        ->required()
                        ->numeric()
                        ->maxValue(fn (Get $get) => (Str::remove(['.'], $get('stock'))))
                        ->live(debounce: 800)
                        ->afterStateUpdated(function (?string $state, $old, Get $get, Set $set) {

                            $stock = Str::remove(['.'], $get('stock'));

                            if ($state > $stock) {
                                self::notificationLimitStock($get('product_name'));
                                $set('quantity', $old);
                                $state = $old;
                            }
                            $price = Str::remove(['.'], $get('price'));
                            $set('totalPrice', self::calculatePriceQuantity($price, $state));
                        })
                        ->minValue(1),

                    TextInput::make('totalPrice')
                        ->label('Precio Total')
                        ->prefix('$')
                        ->default(fn (Get $get, Set $set) => $get('totalPrice'))
                        ->disabled()
                        ->stripCharacters('.')
                        ->dehydrated(),
                ])
                //->defaultItems(1)
                ->mutateRelationshipDataBeforeCreateUsing(function (array $data): array {
                    $data['total'] = $data['totalPrice'];
                    unset($data['totalPrice']);
                    return $data;
                }),

        ];
    }

    public static function searchProduct(string $type, string $state, Get $get, Set $set)
    {
        if (!$get('location_id')) {
            Notification::make()
                ->title('Primero debe elegir la ubicacion')
                ->danger()
                ->send();
        }
        if (!$state) {
            return;
        }

        $stock = Stock::with('product')
            ->where('location_id', $get('location_id'))
            ->where('quantity', '>', 0);

        $stock = match ($type) {
            'barcode' => $stock->whereRelation('product', 'barcode', $state)->first(),
            'stock_id' => $stock->find($state)
        };

        $set($type, null);

        if (!$stock) {
            self::notificationProductNoFound();
            return;
        }
        $listProducts = collect($get('saleProducts'));
        $productSeleted = $listProducts->firstWhere('product_id', $stock->product_id);

        if ($productSeleted) {

            if (($productSeleted['quantity'] + 1) > $stock->quantity) {
                self::notificationLimitStock($stock->product->name);
                return;
            }
            $listProducts = $listProducts->map(function ($item) use ($stock) {
                if ($item['product_id'] == $stock->product_id) {
                    $quantity = $item['quantity'] + 1;
                    $item['quantity'] = $quantity;
                    $item['totalPrice'] = self::calculatePriceQuantity($stock->product->price, $quantity);
                }
                return $item;
            });
            $set('saleProducts', $listProducts->toArray());

            Notification::make()
                ->title('El producto ya esta en la lista , se le agrego +1')
                ->info()
                ->send();
        } else {


            $set('saleProducts', [
                [
                    'product_id' => $stock->product->id,
                    'product_name' => $stock->product->nameBarcode,
                    'stock' => Number::format($stock->quantity),
                    'price' => Number::format($stock->product->price),
                    'quantity' => 1,
                    'totalPrice' => Number::format($stock->product->price),
                ],
                ...$get('saleProducts')
            ]);
        }
        self::updateTotals($get, $set);
    }

    public static function updateTotals(Get $get, Set $set): void
    {
        // Retrieve all selected products and remove empty rows
        $subtotal = SaleService::calculateSubTotal($get('saleProducts'));

        // Update the state with the new values
        $set('sub_total', Number::format($subtotal));
        $set('total', Number::format($subtotal + (int)$get('delivery')));
    }

    public static function notificationLimitStock(string $productName)
    {
        Notification::make()
            ->title("El producto {$productName} - ha alcazado su limite de stock")
            ->danger()
            ->send();
    }

    public static function calculatePriceQuantity($price, $quantity)
    {
        return Number::format($price * $quantity);
    }
    public static function notificationProductNoFound()
    {
        return Notification::make()
            ->title("El producto no esta disponible")
            ->danger()
            ->send();
    }
}
