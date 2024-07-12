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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->string('barcode', 15)->index();
            $table->string('reference', 20)->index();
            $table->text('description_min')->nullable();
            $table->string('img')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->unsignedTinyInteger('discount')->default(0);
            // $table->unsignedInteger('cost')->nullable();
            $table->unsignedMediumInteger('security_stock')->default(5);
            $table->unsignedSmallInteger('max_quantity')->nullable();
            $table->unsignedSmallInteger('min_quantity')->nullable();
            $table->boolean('active')->default(true);
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
