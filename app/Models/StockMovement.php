<?php

namespace App\Models;

use App\Observers\StockMovementObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ObservedBy([StockMovementObserver::class])]
class StockMovement extends Model
{
    use HasFactory;
    protected $casts = [
        'quantity' => 'integer',
    ];
}
