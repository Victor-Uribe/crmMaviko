<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Http\Requests\ProspectOpportunityRequest;
use App\Models\Prospect;
use Illuminate\Http\JsonResponse;


class ProspectOpportunityController extends Controller
{
    public function index(Prospect $prospect): JsonResponse
    {
        $opportunities = $prospect->opportunities()
            ->with('service')
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' =>
                'Oportunidades obtenidas correctamente.',
            'data' => $opportunities,
        ]);
    }

    public function store(
        ProspectOpportunityRequest $request,
        Prospect $prospect
    ): JsonResponse {

        $opportunity = $prospect
            ->opportunities()
            ->create(
                $request->validated()
            );

        $opportunity->load('service');

        return response()->json([
            'message' =>
                'Oportunidad creada correctamente.',
            'data' => $opportunity,
        ], 201);
    }

    public function update(
        ProspectOpportunityRequest $request,
        Prospect $prospect,
        int $opportunityId
    ): JsonResponse {

        $opportunity = $prospect
            ->opportunities()
            ->find($opportunityId);

        if (!$opportunity) {
            return response()->json([
                'message' =>
                    'La oportunidad no existe o no pertenece a este prospecto.',
            ], 404);
        }

        $opportunity->update(
            $request->validated()
        );

        $opportunity->load('service');

        return response()->json([
            'message' =>
                'Oportunidad actualizada correctamente.',
            'data' => $opportunity,
        ]);
    }

    public function destroy(
        Prospect $prospect,
        int $opportunityId
    ): JsonResponse {

        $opportunity = $prospect
            ->opportunities()
            ->find($opportunityId);

        if (!$opportunity) {
            return response()->json([
                'message' =>
                    'La oportunidad no existe o no pertenece a este prospecto.',
            ], 404);
        }

        $opportunity->delete();

        return response()->json([
            'message' =>
                'Oportunidad eliminada correctamente.',
        ]);
    }
}
