<?php

namespace App\Models;

use App\Enums\StockAdjustmentTypeEnum;
use App\Observers\StockAdjustmentObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[ObservedBy([StockAdjustmentObserver::class])]
class StockAdjustment extends Model
{
    use HasFactory;

    protected $casts = [
        // 'data' => 'json',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function locationTo(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_to_id');
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }


    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'stock_adjustment_product')->as('stock')->withPivot([
            'quantity',
            'price',
            'cost'
        ]);
    }

    public function product(): Product
    {
        return $this->products->first();
    }
}
