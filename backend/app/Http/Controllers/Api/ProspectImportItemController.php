<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Prospect;
use App\Models\ProspectImportItem;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;


class ProspectImportItemController extends Controller
{
    public function pending(): JsonResponse
    {
        $items = ProspectImportItem::query()
            ->where('status', 'pending')
            ->with('batch:id,external_id,source,created_at')
            ->orderByDesc('quality_score')
            ->orderByDesc('id')
            ->paginate(20);

        return response()->json([
            'message' =>
                'Prospectos pendientes obtenidos correctamente.',

            'data' => $items,
        ]);
    }

    public function approve(
        ProspectImportItem $prospectImportItem
    ): JsonResponse {

        /*
        |--------------------------------------------------------------------------
        | Validar estado
        |--------------------------------------------------------------------------
        */

        if ($prospectImportItem->status !== 'pending') {
            return response()->json([
                'message' =>
                    'Este prospecto ya fue revisado.',
            ], 422);
        }

        $payload = $prospectImportItem->payload;

        /*
        |--------------------------------------------------------------------------
        | Volvemos a revisar duplicados
        |--------------------------------------------------------------------------
        |
        | Esto es importante porque pudo haber pasado tiempo desde que
        | llegó el prospecto hasta que lo aprobaste.
        |
        */

        $duplicate = Prospect::withTrashed()
            ->where(
                'business_name',
                $payload['business_name']
            )
            ->first();

        if ($duplicate) {

            $prospectImportItem->update([
                'status' => 'duplicate',
                'duplicate_prospect_id' => $duplicate->id,
                'reviewed_at' => now(),
            ]);

            return response()->json([
                'message' =>
                    'No se importó porque ahora existe un prospecto con el mismo nombre.',

                'data' => [
                    'duplicate_prospect_id' =>
                        $duplicate->id,
                ],
            ], 409);
        }

        try {

            $prospect = DB::transaction(
                function () use (
                    $prospectImportItem,
                    $payload
                ) {

                    /*
                    |--------------------------------------------------------------------------
                    | Separar partes del payload
                    |--------------------------------------------------------------------------
                    */

                    $contacts =
                        $payload['contacts'] ?? [];

                    $opportunities =
                        $payload['opportunities'] ?? [];

                    $outreach =
                        $payload['outreach'] ?? [];

                    /*
                    |--------------------------------------------------------------------------
                    | Crear Prospect
                    |--------------------------------------------------------------------------
                    */

                    $prospect = Prospect::create([
                        'business_name' =>
                            $payload['business_name'],

                        'category' =>
                            $payload['category']
                            ?? null,

                        'description' =>
                            $payload['description']
                            ?? null,

                        'country' =>
                            $payload['country']
                            ?? 'México',

                        'state' =>
                            $payload['state']
                            ?? null,

                        'city' =>
                            $payload['city']
                            ?? null,

                        'address' =>
                            $payload['address']
                            ?? null,

                        'source' =>
                            $payload['source']
                            ?? 'integración',

                        'source_url' =>
                            $payload['source_url']
                            ?? null,

                        'opportunity' =>
                            $payload['opportunity']
                            ?? null,

                        'quality_score' =>
                            $payload['quality_score']
                            ?? 0,

                        'stage' =>
                            'new',

                        /*
                        |--------------------------------------------------------------------------
                        | Mensajes sugeridos
                        |--------------------------------------------------------------------------
                        */

                        'whatsapp_message' =>
                            $outreach['whatsapp']
                            ?? null,

                        'email_subject' =>
                            $outreach['email_subject']
                            ?? null,

                        'email_message' =>
                            $outreach['email_body']
                            ?? null,

                        'phone_script' =>
                            $outreach['phone_script']
                            ?? null,
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Crear contactos
                    |--------------------------------------------------------------------------
                    */

                    foreach ($contacts as $contact) {

                        $prospect->contacts()->create([
                            'type' =>
                                $contact['type'],

                            'label' =>
                                $contact['label']
                                ?? null,

                            'value' =>
                                $contact['value'],

                            'normalized_value' =>
                                $this->normalizeContact(
                                    $contact['type'],
                                    $contact['value']
                                ),

                            'is_primary' =>
                                $contact['is_primary']
                                ?? false,

                            'is_verified' =>
                                $contact['is_verified']
                                ?? false,

                            'source_url' =>
                                $contact['source_url']
                                ?? null,

                            'notes' =>
                                $contact['notes']
                                ?? null,
                        ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Crear oportunidades
                    |--------------------------------------------------------------------------
                    */

                    foreach (
                        $opportunities
                        as $opportunity
                    ) {

                        $service = Service::where(
                            'code',
                            $opportunity['service_code']
                        )->first();

                        /*
                        | Si por alguna razón el servicio no existe,
                        | no tiramos todo el prospecto.
                        */

                        if (!$service) {
                            continue;
                        }

                        $prospect
                            ->opportunities()
                            ->create([
                                'service_id' =>
                                    $service->id,

                                'priority' =>
                                    $opportunity['priority']
                                    ?? 'medium',

                                'status' =>
                                    $opportunity['status']
                                    ?? 'detected',

                                'estimated_amount' =>
                                    $opportunity[
                                        'estimated_amount'
                                    ] ?? null,

                                'notes' =>
                                    $opportunity['notes']
                                    ?? null,
                            ]);
                    }

                    /*
                    |--------------------------------------------------------------------------
                    | Marcar import como completado
                    |--------------------------------------------------------------------------
                    */

                    $prospectImportItem->update([
                        'status' => 'imported',

                        'imported_prospect_id' =>
                            $prospect->id,

                        'reviewed_at' =>
                            now(),
                    ]);

                    return $prospect;
                }
            );

            $prospect->load([
                'contacts',
                'opportunities.service',
            ]);

            return response()->json([
                'message' =>
                    'Prospecto aprobado e importado correctamente.',

                'data' =>
                    $prospect,
            ]);

        } catch (Throwable $exception) {

            report($exception);

            return response()->json([
                'message' =>
                    'No fue posible importar el prospecto.',
            ], 500);
        }
    }

    public function reject(
        ProspectImportItem $prospectImportItem
    ): JsonResponse {

        if ($prospectImportItem->status !== 'pending') {
            return response()->json([
                'message' =>
                    'Este prospecto ya fue revisado.',
            ], 422);
        }

        $prospectImportItem->update([
            'status' => 'rejected',
            'reviewed_at' => now(),
        ]);

        return response()->json([
            'message' =>
                'Prospecto rechazado correctamente.',
        ]);
    }

    private function normalizeContact(
        string $type,
        string $value
    ): string {

        return match ($type) {

            'phone',
            'whatsapp' =>
                preg_replace(
                    '/\D+/',
                    '',
                    $value
                ),

            'email' =>
                strtolower(
                    trim($value)
                ),

            default =>
                trim($value),
        };
    }
}
