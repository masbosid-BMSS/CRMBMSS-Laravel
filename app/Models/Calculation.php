<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Calculation extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'ref_no',
        'contact_id',
        'owner_id',
        'type',
        'assets_total',
        'deductions_total',
        'net_amount',
        'nisab_amount',
        'rate',
        'zakat_amount',
        'paid_amount',
        'haul_status',
        'status',
        'calculation_date',
        'items',
        'note',
    ];

    protected $casts = [
        'assets_total' => 'decimal:2',
        'deductions_total' => 'decimal:2',
        'net_amount' => 'decimal:2',
        'nisab_amount' => 'decimal:2',
        'rate' => 'decimal:6',
        'zakat_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
        'calculation_date' => 'date',
        'items' => 'array',
    ];

    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class, 'contact_id');
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'calculation_id');
    }

    public function getRemainingAmountAttribute(): float
    {
        return (float) max(0, $this->zakat_amount - $this->paid_amount);
    }
}
