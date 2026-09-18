<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProspectImportBatch extends Model
{
    //
    protected $fillable = [
        'external_id',
        'source',
        'status',
        'total',
        'ready',
        'duplicates',
        'errors',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function items()
    {
        return $this->hasMany(
            ProspectImportItem::class,
            'batch_id'
        );
    }
}
