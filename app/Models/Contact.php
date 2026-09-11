<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contact extends Model
{
    use SoftDeletes;

    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'niss',
        'name',
        'phone',
        'city',
        'owner_id',
        'wa_account_id',
        'status',
        'relation_status',
        'relationship_note',
        'zakat_status',
        'ltv',
        'program',
        'source',
        'tags',
        'notes',
        'last_activity_at',
        'is_archived',
    ];

    protected $casts = [
        'tags' => 'array',
        'ltv' => 'decimal:2',
        'is_archived' => 'boolean',
        'last_activity_at' => 'datetime',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function waAccount(): BelongsTo
    {
        return $this->belongsTo(WaAccount::class, 'wa_account_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'contact_id')->orderByDesc('transaction_date');
    }

    public function calculations(): HasMany
    {
        return $this->hasMany(Calculation::class, 'contact_id')->orderByDesc('calculation_date');
    }

    public function followups(): HasMany
    {
        return $this->hasMany(Followup::class, 'contact_id')->orderBy('scheduled_at');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class, 'contact_id');
    }

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', trim($this->name));
        $initials = '';
        foreach (array_slice($words, 0, 2) as $word) {
            $initials .= mb_strtoupper(mb_substr($word, 0, 1));
        }
        return $initials ?: 'K';
    }

    public function scopeActive($query)
    {
        return $query->where('is_archived', false);
    }
}
