<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    protected $fillable = [
        'id',
        'name',
        'category',
        'description',
        'status',
    ];

    public function campaigns(): HasMany
    {
        return $this->hasMany(Campaign::class, 'program_id');
    }
}
