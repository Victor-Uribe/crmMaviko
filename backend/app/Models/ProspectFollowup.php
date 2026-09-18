<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class ProspectFollowup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'prospect_id',
        'prospect_contact_id',
        'prospect_opportunity_id',
        'type',
        'title',
        'notes',
        'priority',
        'status',
        'scheduled_at',
        'completed_at',
        'outcome',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function prospect()
    {
        return $this->belongsTo(Prospect::class);
    }

    public function contact()
    {
        return $this->belongsTo(
            ProspectContact::class,
            'prospect_contact_id'
        );
    }

    public function opportunity()
    {
        return $this->belongsTo(
            ProspectOpportunity::class,
            'prospect_opportunity_id'
        );
    }

    public function scopePending(Builder $query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeOverdue(Builder $query)
    {
        return $query
            ->pending()
            ->where('scheduled_at', '<', now());
    }

    public function scopeToday(Builder $query)
    {
        return $query
            ->pending()
            ->whereBetween('scheduled_at', [
                now()->startOfDay(),
                now()->endOfDay(),
            ]);
    }

    public function scopeTodayPending(Builder $query)
    {
        return $query
            ->pending()
            ->whereBetween('scheduled_at', [
                now(),
                now()->endOfDay(),
            ]);
    }

    public function scopeUpcoming(
        Builder $query,
        int $days = 7
    ) {
        return $query
            ->pending()
            ->whereBetween('scheduled_at', [
                now()->copy()->addDay()->startOfDay(),
                now()->copy()->addDays($days)->endOfDay(),
            ]);
    }

}
