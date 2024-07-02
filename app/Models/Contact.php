<?php

namespace App\Models;

use App\Enums\ContactTypesEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $casts = [
        'type' => ContactTypesEnum::class,
    ];
}
