<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    public function stock()
    {
        return $this->hasMany(Stock::class);
    }
    public function history_stock(): HasMany
    {
        return $this->hasMany(StockAdjustment::class);
    }

    public function locations(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'stock')->where('type', 'warehouse')->withPivot(['stock', 'security_stock']);
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Location::class, 'stock')->where('type', 'store')->withPivot(['stock', 'security_stock']);;
    }
}
