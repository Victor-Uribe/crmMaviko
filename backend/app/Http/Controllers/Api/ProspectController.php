<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProspectRequest;
use App\Models\Prospect;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ProspectIndexRequest;

class ProspectController extends Controller
{
    public function index(
        ProspectIndexRequest $request
    ): JsonResponse {

        $filters = $request->validated();

        $query = Prospect::query();

        if (!empty($filters['search'])) {

            $search = $filters['search'];

            $query->where(function ($query) use ($search) {

                $query
                    ->where(
                        'business_name',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'category',
                        'like',
                        "%{$search}%"
                    )
                    ->orWhere(
                        'city',
                        'like',
                        "%{$search}%"
                    );
            });
        }

        if (!empty($filters['stage'])) {
            $query->where(
                'stage',
                $filters['stage']
            );
        }

        if (!empty($filters['city'])) {
            $query->where(
                'city',
                'like',
                "%{$filters['city']}%"
            );
        }

        if (isset($filters['min_score'])) {
            $query->where(
                'quality_score',
                '>=',
                $filters['min_score']
            );
        }

        $perPage = $filters['per_page'] ?? 15;

        $prospects = $query
            ->orderByDesc('id')
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'message' =>
                'Prospectos obtenidos correctamente.',
            'data' => $prospects,
        ]);
    }

    //
    public function store(ProspectRequest $request): JsonResponse
    {
        $prospect = Prospect::create($request->validated());

        return response()->json([
            'message' => 'Prospecto creado correctamente',
            'data' => $prospect,
        ], 201);
    }

    //
    public function show(Prospect $prospect): JsonResponse
    {
        return response()->json([
            'message' => 'Prospecto obtenido correctamente',
            'data' => $prospect,
        ]);
    }

    //
    public function update(ProspectRequest $request, Prospect $prospect): JsonResponse
    {
        $prospect->update($request->validated());

        return response()->json([
            'message' => 'Prospecto actualizado correctamente',
            'data' => $prospect,
        ]);
    }

    //
    public function destroy(Prospect $prospect): JsonResponse
    {
        $prospect->delete();

        return response()->json([
            'message' => 'Prospecto eliminado correctamente',
            'data' => $prospect,
        ]);
    }

    public function trash(): JsonResponse
    {
        $prospects = Prospect::onlyTrashed()
            ->orderByDesc('deleted_at')
            ->paginate(15);

        return response()->json([
            'message' => 'Prospectos eliminados obtenidos correctamente.',
            'data' => $prospects,
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $prospect = Prospect::onlyTrashed()
            ->findOrFail($id);

        $prospect->restore();

        return response()->json([
            'message' => 'Prospecto restaurado correctamente.',
            'data' => $prospect,
        ]);
    }
}
