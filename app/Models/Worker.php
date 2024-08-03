<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Worker extends Model
{
    use HasFactory;

    public function payrolls(): HasMany
    {
        return $this->hasMany(Payroll::class);
    }

    public function payroll_last(): HasOne
    {
        return $this->hasOne(Payroll::class)->latestOfMany();
    }
}
