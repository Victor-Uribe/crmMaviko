<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProspectOpportunityRequest extends FormRequest
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
        $prospect = $this->route('prospect');

        $opportunityId = $this->route('opportunityId');

        $isCreating = $this->isMethod('post');

        return [
            'service_id' => [
                $isCreating ? 'required' : 'sometimes',
                'integer',

                Rule::exists('services', 'id')
                    ->where('is_active', true),

                Rule::unique(
                    'prospect_opportunities',
                    'service_id'
                )
                    ->where(function ($query) use ($prospect) {
                        return $query->where(
                            'prospect_id',
                            $prospect->id
                        );
                    })
                    ->ignore($opportunityId),
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
                    'detected',
                    'offered',
                    'quoted',
                    'negotiating',
                    'won',
                    'lost',
                ]),
            ],

            'estimated_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' =>
                'El servicio es obligatorio.',

            'service_id.exists' =>
                'El servicio seleccionado no existe o está inactivo.',

            'service_id.unique' =>
                'Este servicio ya está registrado como oportunidad para el prospecto.',

            'priority.in' =>
                'La prioridad seleccionada no es válida.',

            'status.in' =>
                'El estado de la oportunidad no es válido.',

            'estimated_amount.numeric' =>
                'El monto estimado debe ser numérico.',

            'estimated_amount.min' =>
                'El monto estimado no puede ser negativo.',
        ];
    }
}
