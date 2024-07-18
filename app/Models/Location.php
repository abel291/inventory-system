<?php

namespace App\Models;

use App\Enums\LocationTypeEnum;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => LocationTypeEnum::class,
    ];

    protected function getNameTypeAttribute()
    {
        return $this->type->getLabel() . " | " . $this->name;
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'stock')->withPivot(['quantity', 'price']);
    }

    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class);
    }
}
