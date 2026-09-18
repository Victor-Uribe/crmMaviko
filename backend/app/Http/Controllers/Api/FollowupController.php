<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ProspectFollowup;
use Illuminate\Http\JsonResponse;

class FollowupController extends Controller
{
    //
    public function today(): JsonResponse
    {
        $followups = ProspectFollowup::query()
            ->todayPending()
            ->with([
                'prospect',
                'contact',
                'opportunity.service',
            ])
            ->orderBy('scheduled_at')
            ->get();

        return response()->json([
            'message' => 'Seguimientos de hoy obtenidos correctamente.',
            'data' => $followups,
        ]);
    }

    public function overdue(): JsonResponse
    {
        $followups = ProspectFollowup::query()
            ->overdue()
            ->with([
                'prospect',
                'contact',
                'opportunity.service',
            ])
            ->orderBy('scheduled_at')
            ->get();

        return response()->json([
            'message' => 'Seguimientos vencidos obtenidos correctamente.',
            'data' => $followups,
        ]);
    }

    public function upcoming(Request $request): JsonResponse
    {
        $days = (int) $request->query('days', 7);

        $days = max(1, min($days, 90));

        $followups = ProspectFollowup::query()
            ->upcoming($days)
            ->with([
                'prospect',
                'contact',
                'opportunity.service',
            ])
            ->orderBy('scheduled_at')
            ->get();

        return response()->json([
            'message' => 'Próximos seguimientos obtenidos correctamente.',
            'days' => $days,
            'data' => $followups,
        ]);
    }
}
