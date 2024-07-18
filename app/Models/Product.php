<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Number;

class Product extends Model
{
    use HasFactory;

    protected function getNameBarcodeAttribute()
    {
        return $this->barcode . " - " . $this->name;
    }

    protected function getNameBarcodePriceAttribute()
    {
        return $this->barcode . " - " . $this->name . " - " . Number::currency($this->price);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'stock')->withPivot(['quantity']);
    }

    public function latestStock()
    {
        return $this->hasOne(Stock::class)->latestOfMany();
    }

    public function stockAdjustments(): BelongsToMany
    {
        return $this->belongsToMany(StockAdjustment::class, 'stock_adjustment_product')->as('stock')->withPivot([
            'quantity',
            'price',

        ]);
    }
}
