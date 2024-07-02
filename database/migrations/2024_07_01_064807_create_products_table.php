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
            $table->string('name');
            $table->string('code');
            $table->string('slug')->unique()->index();
            $table->text('description_min')->nullable();
            $table->string('img')->nullable();
            $table->unsignedInteger('price')->default(0);
            $table->unsignedInteger('cost');
            $table->unsignedTinyInteger('discount')->nullable();
            $table->unsignedInteger('price_discount')->nullable();
            $table->unsignedInteger('stock');
            $table->unsignedSmallInteger('max_quantity');
            $table->unsignedSmallInteger('min_quantity');
            $table->boolean('active')->default(true);
            // $table->foreignId('brand_id')->nullable()->constrained()->nullOnDelete();
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
