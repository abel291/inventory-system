<?php

namespace Database\Seeders;

use App\Models\PaymentMethod;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentMethodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        PaymentMethod::truncate();
        PaymentMethod::create(['name' => 'Efectivo']);
        PaymentMethod::create(['name' => 'Transferencias bancarias']);
        PaymentMethod::create(['name' => 'Tarjeta de crédito']);
        PaymentMethod::create(['name' => 'Tarjeta de débito']);
        PaymentMethod::create(['name' => 'Pagos móviles']);
        PaymentMethod::create(['name' => 'PSE (Pagos Seguros en Línea)']);
        PaymentMethod::create(['name' => 'PayPal']);
        PaymentMethod::create(['name' => 'Mercado Pago']);
        PaymentMethod::create(['name' => 'Efecty']);
        PaymentMethod::create(['name' => 'RapiPago']);
    }
}
