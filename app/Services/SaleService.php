<?php

namespace App\Services;

use App\Enums\StockMovementOperationEnum;
use App\Enums\StockMovementTypeEnum;
use App\Enums\StockStatuEnum;
use App\Models\Product;
use App\Models\Stock;
use App\Models\StockEntry;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Artisan;

class SaleService
{
    public static function generateCode(): string
    {

        return strtoupper(fake()->bothify('??#####'));
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
}
