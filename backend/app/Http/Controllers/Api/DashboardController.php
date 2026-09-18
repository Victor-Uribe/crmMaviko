<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Models\ProspectFollowup;
use App\Models\ProspectOpportunity;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    //
      public function index(): JsonResponse
    {
        $prospects = [
            'total' => Prospect::count(),

            'new' => Prospect::where(
                'stage',
                'new'
            )->count(),

            'contacted' => Prospect::where(
                'stage',
                'contacted'
            )->count(),

            'interested' => Prospect::where(
                'stage',
                'interested'
            )->count(),

            'quoted' => Prospect::where(
                'stage',
                'quoted'
            )->count(),

            'negotiation' => Prospect::where(
                'stage',
                'negotiation'
            )->count(),

            'won' => Prospect::where(
                'stage',
                'won'
            )->count(),

            'lost' => Prospect::where(
                'stage',
                'lost'
            )->count(),
        ];

        $followups = [
            'overdue' => ProspectFollowup::overdue()
                ->count(),

            'today' => ProspectFollowup::todayPending()
                ->count(),

            'upcoming' => ProspectFollowup::upcoming(7)
                ->count(),
        ];

        $openOpportunityStatuses = [
            'detected',
            'offered',
            'quoted',
            'negotiating',
        ];

        $opportunities = [
            'total' => ProspectOpportunity::count(),

            'detected' => ProspectOpportunity::where(
                'status',
                'detected'
            )->count(),

            'offered' => ProspectOpportunity::where(
                'status',
                'offered'
            )->count(),

            'quoted' => ProspectOpportunity::where(
                'status',
                'quoted'
            )->count(),

            'negotiating' => ProspectOpportunity::where(
                'status',
                'negotiating'
            )->count(),

            'won' => ProspectOpportunity::where(
                'status',
                'won'
            )->count(),

            'lost' => ProspectOpportunity::where(
                'status',
                'lost'
            )->count(),

            'potential_amount' => round(
                (float) ProspectOpportunity::whereIn(
                    'status',
                    $openOpportunityStatuses
                )->sum('estimated_amount'),
                2
            ),

            'won_amount' => round(
                (float) ProspectOpportunity::where(
                    'status',
                    'won'
                )->sum('estimated_amount'),
                2
            ),
        ];

        $todayFollowups = ProspectFollowup::query()
            ->todayPending()
            ->with([
                'prospect:id,business_name',
                'contact',
                'opportunity.service',
            ])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $overdueFollowups = ProspectFollowup::query()
            ->overdue()
            ->with([
                'prospect:id,business_name',
                'contact',
                'opportunity.service',
            ])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return response()->json([
            'message' => 'Dashboard obtenido correctamente.',
            'data' => [
                'prospects' => $prospects,
                'followups' => $followups,
                'opportunities' => $opportunities,

                'agenda' => [
                    'today' => $todayFollowups,
                    'overdue' => $overdueFollowups,
                ],
            ],
        ]);
    }
}
