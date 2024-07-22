<?php

namespace App\Models;

use App\Enums\StockStatuEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


class StockEntry extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => StockStatuEnum::class,
        'status_at' => 'timestamp',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function stockEntryProducts(): HasMany
    {
        return $this->hasMany(StockEntryProduct::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'stock_entry_product')
            ->withPivot([
                'quantity',
                'cost'
            ]);
    }
}
