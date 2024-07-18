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
        Schema::create('stock', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('quantity');
            $table->unsignedMediumInteger('price');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_entries', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending'); //aprobado pendiente rechazado
            $table->string('note')->nullable();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); //responsable
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('stock_entry_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('quantity');
            $table->unsignedMediumInteger('cost');
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('stock_entry_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('stoct_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending'); //aprobado pendiente rechazado
            $table->string('note');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_to_id')->constrained()->cascadeOnDelete();
            $table->foreignId('location_from_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('stoct_transfer_product', function (Blueprint $table) {
            $table->id();
            $table->unsignedMediumInteger('quantity');
            $table->foreignId('stoct_transfer_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });


        Schema::create('stock_adjustments', function (Blueprint $table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->text('note');
            $table->unsignedMediumInteger('quantity');
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); //responsable
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // Schema::create('stock_adjustment_product', function (Blueprint $table) {
        // 	$table->id();
        // 	$table->unsignedMediumInteger('quantity');
        // 	$table->unsignedMediumInteger('price')->nullable();
        // 	$table->unsignedMediumInteger('cost')->nullable();
        // 	$table->foreignId('stock_adjustment_id')->constrained()->cascadeOnDelete();
        // 	$table->foreignId('product_id')->constrained()->cascadeOnDelete();
        // 	$table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock');
        Schema::dropIfExists('stock_entries');
        Schema::dropIfExists('stock_entry_product');
        Schema::dropIfExists('stoct_transfers');
        Schema::dropIfExists('stoct_transfer_product');
        Schema::dropIfExists('stock_adjustments');
        // Schema::dropIfExists('stock_adjustment_product');
    }
};
