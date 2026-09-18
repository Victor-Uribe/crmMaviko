<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProspectFollowupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $isCreating = $this->isMethod('post');

        return [
            'prospect_contact_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'prospect_contacts',
                    'id'
                ),
            ],

            'prospect_opportunity_id' => [
                'nullable',
                'integer',
                Rule::exists(
                    'prospect_opportunities',
                    'id'
                ),
            ],

            'type' => [
                $isCreating ? 'required' : 'sometimes',
                Rule::in([
                    'whatsapp',
                    'phone',
                    'email',
                    'meeting',
                    'task',
                ]),
            ],

            'title' => [
                $isCreating ? 'required' : 'sometimes',
                'string',
                'max:255',
            ],

            'notes' => [
                'nullable',
                'string',
            ],

            'priority' => [
                'sometimes',
                Rule::in([
                    'low',
                    'medium',
                    'high',
                ]),
            ],

            'status' => [
                'sometimes',
                Rule::in([
                    'pending',
                    'completed',
                    'cancelled',
                ]),
            ],

            'scheduled_at' => [
                $isCreating ? 'required' : 'sometimes',
                'date',
            ],

            'outcome' => [
                'nullable',
                Rule::in([
                    'responded',
                    'no_response',
                    'interested',
                    'not_interested',
                    'quoted',
                    'rescheduled',
                ]),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' =>
                'El tipo de seguimiento es obligatorio.',

            'type.in' =>
                'El tipo de seguimiento no es válido.',

            'title.required' =>
                'El título del seguimiento es obligatorio.',

            'priority.in' =>
                'La prioridad seleccionada no es válida.',

            'status.in' =>
                'El estado seleccionado no es válido.',

            'scheduled_at.required' =>
                'La fecha del seguimiento es obligatoria.',

            'scheduled_at.date' =>
                'La fecha del seguimiento no tiene un formato válido.',

            'prospect_contact_id.exists' =>
                'El contacto seleccionado no existe.',

            'prospect_opportunity_id.exists' =>
                'La oportunidad seleccionada no existe.',

            'outcome.in' =>
                'El resultado seleccionado no es válido.',
        ];
    }
}
