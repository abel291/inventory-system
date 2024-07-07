<?php

namespace App\Models;

use App\Enums\LocationTypeEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Location extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => LocationTypeEnum::class,
    ];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'stock')->withPivot(['stock', 'security_stock']);
    }
}
