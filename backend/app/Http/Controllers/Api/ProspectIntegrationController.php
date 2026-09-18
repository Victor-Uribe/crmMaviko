<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProspectIntegrationRequest;
use App\Models\Prospect;
use App\Models\ProspectImportBatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;


class ProspectIntegrationController extends Controller
{
    //
   public function store(
        ProspectIntegrationRequest $request
    ): JsonResponse {

        $data = $request->validated();

        /*
        |--------------------------------------------------------------------------
        | Evitar que el mismo lote se registre dos veces
        |--------------------------------------------------------------------------
        */

        $existingBatch = ProspectImportBatch::where(
            'external_id',
            $data['external_id']
        )->first();

        if ($existingBatch) {
            return response()->json([
                'message' => 'Este lote ya fue recibido anteriormente.',
                'data' => [
                    'batch_id' => $existingBatch->id,
                    'external_id' => $existingBatch->external_id,
                    'total' => $existingBatch->total,
                    'ready' => $existingBatch->ready,
                    'duplicates' => $existingBatch->duplicates,
                    'errors' => $existingBatch->errors,
                ],
            ]);
        }

        $batch = DB::transaction(
            function () use ($data) {

                $batch = ProspectImportBatch::create([
                    'external_id' =>
                        $data['external_id'],

                    'source' =>
                        $data['source'],

                    'status' =>
                        'received',

                    'metadata' =>
                        $data['metadata'] ?? null,

                    'total' =>
                        count($data['prospects']),
                ]);

                $ready = 0;
                $duplicates = 0;
                $errors = 0;

                foreach ($data['prospects'] as $prospectData) {

                    try {

                        /*
                        |--------------------------------------------------------------------------
                        | Buscar duplicado
                        |--------------------------------------------------------------------------
                        |
                        | Por ahora usamos business_name.
                        |
                        | Después mejoraremos esto usando:
                        | - WhatsApp
                        | - teléfono
                        | - dominio
                        | - ciudad
                        | - nombre similar
                        |
                        */

                        $duplicate = Prospect::withTrashed()
                            ->where(
                                'business_name',
                                $prospectData['business_name']
                            )
                            ->first();

                        if ($duplicate) {

                            $batch->items()->create([
                                'business_name' =>
                                    $prospectData['business_name'],

                                'quality_score' =>
                                    $prospectData['quality_score']
                                    ?? 0,

                                'status' =>
                                    'duplicate',

                                'duplicate_prospect_id' =>
                                    $duplicate->id,

                                'payload' =>
                                    $prospectData,
                            ]);

                            $duplicates++;

                            continue;
                        }

                        /*
                        |--------------------------------------------------------------------------
                        | Prospecto listo para revisión
                        |--------------------------------------------------------------------------
                        */

                        $batch->items()->create([
                            'business_name' =>
                                $prospectData['business_name'],

                            'quality_score' =>
                                $prospectData['quality_score']
                                ?? 0,

                            'status' =>
                                'pending',

                            'payload' =>
                                $prospectData,
                        ]);

                        $ready++;

                    } catch (\Throwable $exception) {

                        report($exception);

                        $batch->items()->create([
                            'business_name' =>
                                $prospectData['business_name']
                                ?? 'Sin nombre',

                            'quality_score' =>
                                $prospectData['quality_score']
                                ?? 0,

                            'status' =>
                                'error',

                            'payload' =>
                                $prospectData,

                            'error_message' =>
                                $exception->getMessage(),
                        ]);

                        $errors++;
                    }
                }

                $batch->update([
                    'ready' => $ready,
                    'duplicates' => $duplicates,
                    'errors' => $errors,
                ]);

                return $batch;
            }
        );

        return response()->json([
            'message' =>
                'Lote de prospectos recibido correctamente.',

            'data' => [
                'batch_id' =>
                    $batch->id,

                'external_id' =>
                    $batch->external_id,

                'total' =>
                    $batch->total,

                'ready' =>
                    $batch->ready,

                'duplicates' =>
                    $batch->duplicates,

                'errors' =>
                    $batch->errors,
            ],
        ], 201);
    }
}
