<?php

namespace App\Models;

use App\Enums\StockStatuEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockTransfer extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => StockStatuEnum::class,
        'status_at' => 'timestamp',
    ];

    public function locationFrom(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
    public function locationTo(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function userApprove(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_approve_id');
    }
    public function userRequest(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_request_id');
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'stock_transfer_product')->withPivot(['quantity']);
    }

    public function stockTransferProduct(): HasMany
    {
        return $this->hasMany(StockTransferProduct::class);
    }
}
