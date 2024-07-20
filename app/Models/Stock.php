<?php

namespace App\Models;

use App\Observers\StockObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;


class Stock extends Model
{
    use HasFactory;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $table = 'stock';
    public $incrementing = true;
    protected $casts = [
        'quantity' => 'integer',
    ];
    protected $attributes = [
        'quantity' => 0,
    ];
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
