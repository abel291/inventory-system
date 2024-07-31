<?php

use App\Enums\SalePaymentTypeEnum;
use App\Enums\SaleStatuEnum;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique()->index();
            $table->string('status')->default(SaleStatuEnum::ACCEPTED->value);
            $table->string('payment_type')->default(SalePaymentTypeEnum::CASH->value);
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->unsignedInteger('tax_value')->default(0);
            $table->unsignedTinyInteger('tax_rate')->default(0);
            $table->unsignedInteger('delivery')->default(0);
            $table->decimal('total', 12, 2);
            $table->json('data')->nullable();
            $table->json('discount')->nullable();
            $table->json('refund')->nullable();
            $table->timestamp('refund_at')->nullable();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); //vendedor

            $table->timestamps();
        });

        Schema::create('sale_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('sale_id')->constrained()->cascadeOnDelete();
            $table->unsignedMediumInteger('price');
            $table->unsignedMediumInteger('quantity');
            $table->unsignedMediumInteger('total');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
        Schema::dropIfExists('sale_products');
    }
};
