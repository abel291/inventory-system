<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class StockTransfer extends Model
{
    use HasFactory;

    public function locationTo(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
    public function locationFrom(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'stoct_transfer_product')->as('stock')->withPivot([]);
    }
}
