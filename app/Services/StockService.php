<?php

namespace App\Services;

use App\Enums\SaleStatuEnum;
use App\Enums\StockMovementOperationEnum;
use App\Enums\StockMovementTypeEnum;
use App\Enums\StockStatuEnum;
use App\Models\Sale;
use App\Models\Stock;
use App\Models\StockEntry;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Artisan;

class StockService
{
    public static function stockEntryAddition(StockEntry $stockEntry)
    {

        if ($stockEntry->status != StockStatuEnum::ACCEPTED) {
            return;
        };

        foreach ($stockEntry->products as $product) {

            StockMovement::create([
                'location_id' => $stockEntry->location_id,
                'product_id' => $product->id,
                'type' => StockMovementTypeEnum::ENTRY,
                'operation' => StockMovementOperationEnum::ADDITION,
                'quantity' => $product->pivot->quantity,
            ]);
        }
    }

    public static function stockTransfer(StockTransfer $stockTransfer)
    {

        if ($stockTransfer->status != StockStatuEnum::ACCEPTED) {
            return;
        };

        foreach ($stockTransfer->products as $product) {


            StockMovement::create([
                'location_id' => $stockTransfer->location_from_id,
                'product_id' => $product->id,
                'type' => StockMovementTypeEnum::TRANSFER,
                'operation' => StockMovementOperationEnum::SUBTRACTION,
                'quantity' => $product->pivot->quantity,
            ]);

            StockMovement::create([
                'location_id' => $stockTransfer->location_to_id,
                'product_id' => $product->id,
                'type' => StockMovementTypeEnum::TRANSFER,
                'operation' => StockMovementOperationEnum::ADDITION,
                'quantity' => $product->pivot->quantity,
            ]);
        }
    }
    public static function sale(Sale $sale)
    {
        foreach ($sale->saleProducts as  $item) {

            $type_opeation = [];
            switch ($sale->status) {
                case SaleStatuEnum::ACCEPTED:
                    $type_opeation = [
                        'type' => StockMovementTypeEnum::SALE,
                        'operation' => StockMovementOperationEnum::SUBTRACTION,
                    ];
                    break;
                case SaleStatuEnum::CANCELLED:
                    $type_opeation = [
                        'type' => StockMovementTypeEnum::SALE_CANCELLED,
                        'operation' => StockMovementOperationEnum::ADDITION,
                    ];
                    break;
                case SaleStatuEnum::REFUNDED:
                    $type_opeation = [
                        'type' => StockMovementTypeEnum::SALE_REFUND,
                        'operation' => StockMovementOperationEnum::ADDITION,
                    ];
                    break;
            }

            $stock = StockMovement::create([
                'location_id' => $sale->location_id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                ...$type_opeation
            ]);
        }
    }
}
