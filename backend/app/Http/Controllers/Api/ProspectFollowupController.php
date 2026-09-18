<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProspectFollowupRequest;
use App\Models\Prospect;
use Illuminate\Http\JsonResponse;

class ProspectFollowupController extends Controller
{
    //
    public function index(Prospect $prospect): JsonResponse
    {
        $followups = $prospect->followups()
            ->with([
                'contact',
                'opportunity.service',
            ])
            ->orderBy('scheduled_at')
            ->get();

        return response()->json([
            'message' => 'Seguimientos obtenidos correctamente.',
            'data' => $followups,
        ]);
    }

    public function store(
        ProspectFollowupRequest $request,
        Prospect $prospect
    ): JsonResponse {

        $data = $request->validated();

        $error = $this->validateRelations(
            $prospect,
            $data
        );

        if ($error) {
            return $error;
        }

        $followup = $prospect
            ->followups()
            ->create($data);

        $followup->load([
            'contact',
            'opportunity.service',
        ]);

        return response()->json([
            'message' => 'Seguimiento creado correctamente.',
            'data' => $followup,
        ], 201);
    }

    public function update(
        ProspectFollowupRequest $request,
        Prospect $prospect,
        int $followupId
    ): JsonResponse {

        $followup = $prospect
            ->followups()
            ->find($followupId);

        if (!$followup) {
            return response()->json([
                'message' =>
                    'El seguimiento no existe o no pertenece a este prospecto.',
            ], 404);
        }

        $data = $request->validated();

        $error = $this->validateRelations(
            $prospect,
            $data
        );

        if ($error) {
            return $error;
        }

        if (
            ($data['status'] ?? null) === 'completed'
            && !$followup->completed_at
        ) {
            $data['completed_at'] = now();
        }

        if (
            isset($data['status'])
            && $data['status'] !== 'completed'
        ) {
            $data['completed_at'] = null;
        }

        $followup->update($data);

        $followup->load([
            'contact',
            'opportunity.service',
        ]);

        return response()->json([
            'message' =>
                'Seguimiento actualizado correctamente.',
            'data' => $followup,
        ]);
    }

    public function destroy(
        Prospect $prospect,
        int $followupId
    ): JsonResponse {

        $followup = $prospect
            ->followups()
            ->find($followupId);

        if (!$followup) {
            return response()->json([
                'message' =>
                    'El seguimiento no existe o no pertenece a este prospecto.',
            ], 404);
        }

        $followup->delete();

        return response()->json([
            'message' =>
                'Seguimiento eliminado correctamente.',
        ]);
    }

    private function validateRelations(
        Prospect $prospect,
        array $data
    ): ?JsonResponse {

        if (!empty($data['prospect_contact_id'])) {

            $contactExists = $prospect
                ->contacts()
                ->where(
                    'id',
                    $data['prospect_contact_id']
                )
                ->exists();

            if (!$contactExists) {
                return response()->json([
                    'message' =>
                        'El contacto no pertenece a este prospecto.',
                ], 422);
            }
        }

        if (!empty($data['prospect_opportunity_id'])) {

            $opportunityExists = $prospect
                ->opportunities()
                ->where(
                    'id',
                    $data['prospect_opportunity_id']
                )
                ->exists();

            if (!$opportunityExists) {
                return response()->json([
                    'message' =>
                        'La oportunidad no pertenece a este prospecto.',
                ], 422);
            }
        }

        return null;
    }

}
