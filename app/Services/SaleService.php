<?php

namespace App\Services;

use App\Enums\StockMovementOperationEnum;
use App\Enums\StockMovementTypeEnum;
use App\Enums\StockStatuEnum;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockEntry;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class SaleService
{
    public static function generateCode($key = ''): string
    {
        $week = strtoupper(now()->isoFormat('dd'));
        return $week . Str::padLeft($key . fake()->bothify('###'), 6, '0');
    }

    public static function calculateSubTotal($products): string
    {

        $selectedProducts = collect($products)->filter(fn ($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');

        $subtotal = $selectedProducts->reduce(function ($subtotal, $product) use ($prices) {

            return $subtotal + ($prices[$product['product_id']] * $product['quantity']);
        }, 0);



        return $subtotal;
    }

    public static function calculateTotal($sale): Sale
    {

        if ($sale->discount) {
            $discount_amount =  $sale->subtotal * ($sale->discount['percent'] / 100);
            $sale->discount = [
                'percent' => $sale->discount['percent'],
                'amount' => $discount_amount
            ];
            $subtotalWithDiscount = $sale->subtotal - $sale->discount['amount'];
        } else {
            $subtotalWithDiscount = $sale->subtotal;
        }

        $sale->total = ($subtotalWithDiscount + $sale->delivery);

        return $sale;
    }
}
