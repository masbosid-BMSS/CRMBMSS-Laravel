<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WaAccount extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'slot',
        'label',
        'phone',
        'capacity',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class, 'wa_account_id');
    }

    public function getAssignedCountAttribute(): int
    {
        return $this->contacts()->where('is_archived', false)->count();
    }

    public function getRemainingCapacityAttribute(): int
    {
        return max(0, $this->capacity - $this->assigned_count);
    }
}
