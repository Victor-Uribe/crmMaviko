<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\ProspectContactRequest;
use App\Models\Prospect;
use App\Models\ProspectContact;
use Illuminate\Http\JsonResponse;


class ProspectContactController extends Controller
{
    //
    public function index(Prospect $prospect): JsonResponse
    {
        $contacts = $prospect->contacts()
            ->orderByDesc('is_primary')
            ->orderBy('type')
            ->get();

        return response()->json([
            'message' => 'Contactos obtenidos correctamente.',
            'data' => $contacts,
        ]);
    }

    public function store(
        ProspectContactRequest $request,
        Prospect $prospect
    ): JsonResponse {

        $data = $request->validated();

        $data['normalized_value'] = $this->normalizeValue(
            $data['type'],
            $data['value']
        );

        $contact = $prospect->contacts()->create($data);

        return response()->json([
            'message' => 'Contacto creado correctamente.',
            'data' => $contact,
        ], 201);
    }

    public function update(
        ProspectContactRequest $request,
        Prospect $prospect,
        int $contactId
    ): JsonResponse {

        $contact = $prospect->contacts()
            ->find($contactId);

        if (!$contact) {
            return response()->json([
                'message' => 'El contacto no existe o no pertenece a este prospecto.',
            ], 404);
        }

        $data = $request->validated();

        if (
            array_key_exists('type', $data) ||
            array_key_exists('value', $data)
        ) {
            $type = $data['type'] ?? $contact->type;
            $value = $data['value'] ?? $contact->value;

            $data['normalized_value'] = $this->normalizeValue(
                $type,
                $value
            );
        }

        if ($data['is_primary'] ?? false) {
            $type = $data['type'] ?? $contact->type;

            $prospect->contacts()
                ->where('type', $type)
                ->where('id', '!=', $contact->id)
                ->update([
                    'is_primary' => false,
                ]);
        }

        $contact->update($data);

        return response()->json([
            'message' => 'Contacto actualizado correctamente.',
            'data' => $contact,
        ]);
    }

    public function destroy(
        Prospect $prospect,
        int $contactId
    ): JsonResponse {

        $contact = $prospect->contacts()
            ->find($contactId);

        if (!$contact) {
            return response()->json([
                'message' => 'El contacto no existe o no pertenece a este prospecto.',
            ], 404);
        }

        $contact->delete();

        return response()->json([
            'message' => 'Contacto eliminado correctamente.',
        ]);
}

    private function normalizeValue(string $type, string $value): string
    {
        return match ($type) {

            'whatsapp',
            'phone' => preg_replace('/\D+/', '', $value),

            'email' => strtolower(trim($value)),

            default => trim($value),
        };
    }

}
