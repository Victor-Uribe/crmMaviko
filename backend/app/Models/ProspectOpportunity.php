<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectOpportunity extends Model
{
    protected $fillable = [
        'prospect_id',
        'service_id',
        'priority',
        'status',
        'estimated_amount',
        'notes',
    ];

    protected $casts = [
        'estimated_amount' => 'decimal:2',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }
}
