<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Campaign extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'program_id',
        'target_amount',
        'achieved_amount',
        'donors_count',
        'new_donors_count',
        'days_remaining',
        'start_date',
        'end_date',
        'is_global',
        'status',
        'description',
        'accent_color',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'achieved_amount' => 'decimal:2',
        'is_global' => 'boolean',
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'campaign_id');
    }

    public function getProgressPercentAttribute(): float
    {
        if ($this->target_amount <= 0) return 0.0;
        return round(min(100.0, ($this->achieved_amount / $this->target_amount) * 100), 1);
    }
}
