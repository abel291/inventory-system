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
        return $this->belongsToMany(Location::class, 'stock')
            ->wherePivot('type', 'total')
            ->wherePivot('remaining', '>', 0)
            ->withPivot(['quantity', 'remaining', 'cost']);
    }

    public function latestStock()
    {
        return $this->hasOne(Stock::class)
            ->latestOfMany()
            ->where('remaining', '>', 0);
    }

    public function totalStock(): HasMany
    {
        return $this->hasMany(Stock::class)->where('type', 'total')->where('remaining', '>', 0);
    }
}
