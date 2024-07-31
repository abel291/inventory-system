<?php

namespace App\Filament\Resources\SaleResource\Pages;

use App\Enums\SalePaymentTypeEnum;
use App\Filament\Resources\SaleResource;
use App\Models\Product;
use App\Models\Sale;
use App\Models\StockMovement;
use App\Services\SaleService;
use App\Services\StockService;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CreateSale extends CreateRecord
{
    protected static string $resource = SaleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['discount'] = $data['discount']['percent'] ? $data['discount'] : null;
        $data['payment'] = array_key_exists('payment', $data) ? $data['payment'] : null;
        return $data;
    }

    protected function getCreateFormAction(): Action
    {
        return parent::getCreateFormAction()->extraAttributes(['type' => 'button', 'wire:click' => 'create']);
    }

    protected function handleRecordCreation(array $data): Model
    {
        // dd($data);
        $saleProducts = $data['saleProducts'];
        $payment = $data['payment'];
        $discount = $data['discount'];

        unset(
            $data['payment'],
            $data['saleProducts'],
            $data['discount']
        );

        $sale = new Sale($data);

        $saleProducts = self::calculateTotalQuantityProduct($saleProducts);

        $sale->subtotal = $saleProducts->sum('total');

        if ($discount) {
            $discount['amount'] = $sale->subtotal * ($discount['percent'] / 100);
            $subtotalWithDiscount = $sale->subtotal - $discount['amount'];
        } else {
            $subtotalWithDiscount = $sale->subtotal;
        }
        $sale->discount = $discount;

        $sale->total = ($subtotalWithDiscount + (int)$data['delivery']);

        $sale->save();

        $sale->saleProducts()->createMany($saleProducts->toArray());

        if ($sale->payment_type == SalePaymentTypeEnum::CASH) {
            $payment['amount'] = $sale->total;
            // dd($payment);
            $sale->payments()->create($payment);
        }

        StockService::sale($sale);

        return $sale;
    }

    public static function calculateTotalQuantityProduct(array $products): Collection
    {
        $selectedProducts = collect($products)->filter(fn ($item) => !empty($item['product_id']) && !empty($item['quantity']));

        $prices = Product::find($selectedProducts->pluck('product_id'))->pluck('price', 'id');

        return  $selectedProducts->map(function (array $item) use ($prices) {
            $price = $prices[$item['product_id']];
            $total = round($prices[$item['product_id']] * $item['quantity']);
            $item['price'] = $price;
            $item['total'] = $total;
            return $item;
        });
    }
}
