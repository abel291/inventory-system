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
        return $this->belongsToMany(Product::class, 'stock')
            ->wherePivot('type', 'total')
            ->wherePivot('remaining', '>', 0)
            ->withPivot(['quantity', 'remaining', 'price', 'cost']);
    }

    public function stock(): HasMany
    {
        return $this->hasMany(Stock::class)->where('type', 'total')->where('remaining', '>', 0);
    }
}
