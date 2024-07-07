<?php

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
        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('initial_stock');
            $table->unsignedMediumInteger('adjustment');
            $table->unsignedMediumInteger('final_stock');
            $table->string('type');
            $table->string('status'); //aprobado pendiente rechasado
            $table->string('note')->nullable();
            $table->boolean('approved');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_id')->constrained()->cascadeOnDelete();

            // $table->string('type'); //ajuste , traslado ,venta
            // $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            // $table->foreignId('location_to_id')->nullable()->constrained()->cascadeOnDelete();
            // $table->foreignId('location_from_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });

        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->unsignedMediumInteger('stock');
            $table->unsignedMediumInteger('security_stock');
            // $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_adjustments');
        Schema::dropIfExists('stock');
    }
};
