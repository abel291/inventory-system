<?php

namespace Database\Seeders;

use App\Enums\ContactTypesEnum;
use App\Enums\SalePaymentTypeEnum;
use App\Enums\SaleStatuEnum;
use App\Models\Contact;
use App\Models\Location;
use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Sale;
use App\Models\SaleProduct;
use App\Models\Stock;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\SaleService;
use App\Services\StockService;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class SaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        Sale::truncate();
        SaleProduct::truncate();
        Payment::truncate();

        $locations = Location::get();
        $users = User::get();
        $paymetMethod = PaymentMethod::get();
        foreach (Contact::where('type', ContactTypesEnum::CLIENT)->get() as $key => $client) {

            $location = $locations->random();
            $sale = new Sale([
                'code' => SaleService::generateCode($key),
                'status' => SaleStatuEnum::ACCEPTED,
                'payment_type' => fake()->randomElement(SalePaymentTypeEnum::cases()),
                'subtotal' => '',
                // 'tax_value' => '',
                // 'tax_rate' => '',
                'delivery' => fake()->randomElement([0, (rand(100, 400) * 100)]),
                'total' => '',
                'discount' => null,
                // 'refund' => '',
                // 'refund_at' => '',
                'contact_id' => $client->id,
                'location_id' => $location->id,
                'user_id' => $users->random()->id,
                'created_at' => fake()->dateTimeBetween('-1 month', '+1 month'),

            ]);

            $stock = Stock::with('product')->where([
                ['location_id', $location->id],
                ['quantity', '>', 0],
            ])->get();

            $productCount = rand(1, min(5, $stock->count()));

            $saleProducts = $stock->random($productCount)->map(function ($item) {
                $price = $item->product->price;
                $quantity = rand(1, min(3, $item->quantity));
                return [
                    'product_id' => $item->product->id,
                    'price' => $price,
                    'quantity' => $quantity,
                    'total' => $price * $quantity,
                ];
            });

            $sale->subtotal = $saleProducts->sum('total');

            if (rand(1, 10) > 9) {
                $sale->discount = [
                    'percent' => fake()->randomElement(range(1, 60))
                ];
            }

            $sale = SaleService::calculateTotal($sale);

            $sale->save();

            $sale->saleProducts()->createMany($saleProducts->toArray());

            if ($sale->payment_type == SalePaymentTypeEnum::CASH) {
                Payment::create([
                    'sale_id' => $sale->id,
                    'payment_method_id' => $paymetMethod->random()->id,
                    'amount' => $sale->total,
                    'reference' => fake()->bothify('#######'),
                    'note' => fake()->randomElement([null, fake()->sentence(3)]),
                ]);
            }

            if ($sale->payment_type == SalePaymentTypeEnum::CREDIT) {

                while ($sale->pendingPayments() >= 0) {
                    Payment::create([
                        'sale_id' => $sale->id,
                        'payment_method_id' => $paymetMethod->random()->id,
                        'amount' => rand(1, $sale->pendingPayments()),
                        'reference' => fake()->bothify('#######'),
                        'note' => fake()->randomElement([null, fake()->sentence(3)]),
                    ]);
                }
            }

            StockService::sale($sale);

            if (rand(1, 10) >= 9) {
                $sale->status = fake()->randomElement([SaleStatuEnum::REFUNDED, SaleStatuEnum::CANCELLED]);
                $sale->refund_at = now();
                $sale->save();
                StockService::sale($sale);
            }

            $this->command->info('Venta:' . $sale->code . " -> " . Number::currency($sale->total));
        }
    }
}
