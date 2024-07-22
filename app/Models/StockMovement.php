<?php

namespace App\Models;

use App\Enums\StockMovementOperationEnum;
use App\Enums\StockMovementTypeEnum;
use App\Observers\StockMovementObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([StockMovementObserver::class])]
class StockMovement extends Model
{
    use HasFactory;
    protected $casts = [
        'old_quantity' => 'integer',
        'quantity' => 'integer',
        'type' => StockMovementTypeEnum::class,
        'operation' => StockMovementOperationEnum::class
    ];
    protected $attributes = [
        'old_quantity' => 0,
        'quantity' => 0,
    ];
}
