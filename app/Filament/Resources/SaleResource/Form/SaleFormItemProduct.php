<?php

namespace App\Filament\Resources\SaleResource\Form;

use App\Forms\Components\SaleProductList;
use App\Models\Product;
use App\Models\Stock;
use App\Services\SaleService;
use Filament\Forms\Components\Actions\Action;
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
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class SaleFormItemProduct
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
                    ->columnSpan(3)
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
                ->required()
                ->label('')
                ->default([])
                ->columnSpanFull()
                ->columns(8)
                ->addable(false)
                ->reorderable(false)
                ->live(debounce: 300)
                ->afterStateUpdated(function (Get $get, Set $set) {
                    self::updateTotals($get, $set);
                })
                ->deleteAction(
                    fn (Action $action) => $action->after(fn (Get $get, Set $set) => self::updateTotals($get, $set)),
                )
                ->schema([
                    Hidden::make('product_id'),
                    TextInput::make('product_name')
                        ->label('Producto')
                        ->disabled()
                        ->columnSpan(4),

                    TextInput::make('stock')
                        ->label('Existencia')
                        ->disabled(),

                    TextInput::make('price')
                        ->label('Precio')
                        ->disabled(),

                    TextInput::make('quantity')
                        ->label('Cantidad')
                        ->default(1)
                        ->required()
                        ->numeric()
                        ->maxValue(fn (Get $get) => (Str::remove(['.'], $get('stock'))))
                        ->minValue(1),

                    TextInput::make('totalPrice')
                        ->label('Precio Total')
                        ->disabled(),
                ]),

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
        $saleProducts = collect($get('saleProducts'));
        $productSeleted = $saleProducts->firstWhere('product_id', $stock->product_id);

        if ($productSeleted) {

            if (($productSeleted['quantity'] + 1) > $stock->quantity) {
                self::notificationLimitStock($stock->product->name);
                return;
            }
            $saleProducts = $saleProducts->map(function ($item) use ($stock) {
                if ($item['product_id'] == $stock->product_id) {
                    $quantity = $item['quantity'] + 1;
                    $item['quantity'] = $quantity;
                    // $item['totalPrice'] = self::calculatePriceQuantity($stock->product->price, $quantity);
                }
                return $item;
            });

            Notification::make()
                ->title('El producto ya esta en la lista , se le agrego +1')
                ->info()
                ->send();
        } else {

            $saleProducts->push([
                'product_id' => $stock->product->id,
                'product_name' => $stock->product->nameBarcode,
                'stock' => $stock->quantity,
                'quantity' => 1,
            ]);
        }

        $set('saleProducts', $saleProducts->toArray());

        self::updateTotals($get, $set);
    }

    public static function updateTotals(Get $get, Set $set): void
    {

        $selectedProducts = self::validateData($get('saleProducts'));

        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');

        $selectedProducts = $selectedProducts->map(function (array $item, int $key) use ($prices) {

            $price = $prices[$item['product_id']];
            $totalPrice = ($prices[$item['product_id']] * $item['quantity']);
            $item['price'] = "$ " . Number::format($price);
            $item['totalPrice'] = "$ " . Number::format($totalPrice);
            return $item;
        });

        $subtotal = $selectedProducts->sum(function (array $item) {
            return Str::remove(['.', '$'], $item['totalPrice']);
        });


        if ($get('discount.percent') && $subtotal > 0) {

            $discount_amount = $subtotal * ($get('discount.percent') / 100);
        } else {
            $discount_amount = 0;
        }

        $total = ($subtotal + (int)$get('delivery')) - $discount_amount;

        $set('saleProducts', $selectedProducts->toArray());

        $set('subtotal', $subtotal);

        $set('discount.amount', $discount_amount);

        $set('total', $total);
    }
    public static function validateData($products)
    {
        return  collect($products)
            ->filter(fn ($item) => !empty($item['product_id']) && !empty($item['quantity']))
            ->map(function ($item) {

                if ($item['quantity'] > $item['stock']) {
                    $item['quantity'] = $item['stock'];
                    self::notificationLimitStock($item['product_name']);
                }
                return $item;
            });
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
