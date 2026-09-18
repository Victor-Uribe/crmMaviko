<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProspectContact extends Model
{
    //
    protected $fillable = [
        'prospect_id',
        'type',
        'label',
        'value',
        'normalized_value',
        'is_primary',
        'is_verified',
        'source_url',
        'notes',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }
}
