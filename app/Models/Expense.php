<?php

namespace App\Models;

use Filament\Forms\Components\BelongsToSelect;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    public function ExpenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class);
    }
}
