<?php

namespace App\Models;

use App\Enums\SalePaymentTypeEnum;
use App\Enums\SaleStatuEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Sale extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => SaleStatuEnum::class,
        'payment_type' => SalePaymentTypeEnum::class,
        'discount' => 'json',
    ];

    public function saleProducts(): HasMany
    {
        return $this->hasMany(SaleProduct::class);
    }
    public function client(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'sale_products')->withPivot(['price', 'quantity', 'total']);
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payment(): HasOne
    {
        return $this->hasOne(Payment::class);
    }
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function totalPayments(): int
    {
        return $this->payments()->sum('amount');
    }

    public function pendingPayments(): int
    {
        return ($this->total - $this->totalPayments());
    }
}
