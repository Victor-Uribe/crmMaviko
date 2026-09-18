<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectImportItem extends Model
{
    //
    protected $fillable = [
        'batch_id',
        'business_name',
        'quality_score',
        'status',
        'duplicate_prospect_id',
        'imported_prospect_id',
        'payload',
        'error_message',
        'reviewed_at',
    ];

    protected $casts = [
        'payload' => 'array',
        'reviewed_at' => 'datetime',
    ];

    public function batch()
    {
        return $this->belongsTo(
            ProspectImportBatch::class,
            'batch_id'
        );
    }

    public function duplicateProspect()
    {
        return $this->belongsTo(
            Prospect::class,
            'duplicate_prospect_id'
        );
    }

    public function importedProspect()
    {
        return $this->belongsTo(
            Prospect::class,
            'imported_prospect_id'
        );
    }
}
