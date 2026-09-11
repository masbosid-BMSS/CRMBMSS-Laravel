<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KpiTarget extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'user_id',
        'start_date',
        'tier',
        'daily_target',
        'followup_target',
        'conversion_target',
        'retention_target',
    ];

    protected $casts = [
        'start_date' => 'date',
        'daily_target' => 'decimal:2',
        'conversion_target' => 'decimal:2',
        'retention_target' => 'decimal:2',
        'tier' => 'integer',
        'followup_target' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
