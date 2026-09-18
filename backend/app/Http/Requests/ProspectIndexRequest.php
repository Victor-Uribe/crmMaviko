<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ProspectIndexRequest extends FormRequest
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
        return [
            'search' => [
                'nullable',
                'string',
                'max:255',
            ],

            'stage' => [
                'nullable',
                Rule::in([
                    'new',
                    'contacted',
                    'interested',
                    'quoted',
                    'negotiation',
                    'won',
                    'lost',
                    'discarded',
                ]),
            ],

            'city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'min_score' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'per_page' => [
                'nullable',
                'integer',
                'min:5',
                'max:100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'stage.in' =>
                'La etapa seleccionada no es válida.',

            'min_score.integer' =>
                'La calificación mínima debe ser un número.',

            'min_score.min' =>
                'La calificación mínima no puede ser menor a 0.',

            'min_score.max' =>
                'La calificación mínima no puede ser mayor a 100.',

            'per_page.integer' =>
                'La cantidad por página debe ser un número.',

            'per_page.min' =>
                'La cantidad mínima por página es 5.',

            'per_page.max' =>
                'La cantidad máxima por página es 100.',
        ];
    }
}
