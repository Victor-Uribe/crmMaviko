<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Prospect extends Model
{
    use SoftDeletes;
    //
    protected $fillable = [
        'business_name',
        'category',
        'description',
        'country',
        'state',
        'city',
        'address',
        'source',
        'source_url',
        'opportunity',
        'quality_score',
        'stage',
        'whatsapp_message',
        'email_subject',
        'email_message',
        'phone_script',
    ];

    public function contacts()
    {
        return $this->hasMany(ProspectContact::class);
    }

    public function opportunities()
    {
        return $this->hasMany(
            ProspectOpportunity::class
        );
    }

    public function followups()
    {
        return $this->hasMany(
            ProspectFollowup::class
        );
    }
}
