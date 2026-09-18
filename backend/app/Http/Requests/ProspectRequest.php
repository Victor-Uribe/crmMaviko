<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Validation\Rule;

class ProspectRequest extends FormRequest
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

        return [
            'business_name' => [
                $prospect ? 'sometimes' : 'required',
                'string',
                'max:255',

                Rule::unique('prospects', 'business_name')
                    ->ignore($prospect?->id),
            ],

            'category' => [
                'nullable',
                'string',
                'max:255',
            ],

            'description' => [
                'nullable',
                'string',
            ],

            'country' => [
                'nullable',
                'string',
                'max:255',
            ],

            'state' => [
                'nullable',
                'string',
                'max:255',
            ],

            'city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'address' => [
                'nullable',
                'string',
            ],

            'source' => [
                'nullable',
                'string',
                'max:255',
            ],

            'source_url' => [
                'nullable',
                'url',
            ],

            'opportunity' => [
                'nullable',
                'string',
            ],

            'quality_score' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'stage' => [
                'nullable',
                'string',
                'max:50',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'business_name.required' => 'El nombre del negocio es obligatorio.',
            'business_name.string' => 'El nombre del negocio debe ser texto.',
            'business_name.max' => 'El nombre del negocio no puede tener más de 255 caracteres.',
            'business_name.unique' => 'Ya existe un prospecto con este nombre.',


            'category.string' => 'La categoría debe ser texto.',
            'category.max' => 'La categoría no puede tener más de 255 caracteres.',

            'source_url.url' => 'La URL de origen no tiene un formato válido.',

            'quality_score.integer' => 'La calificación debe ser un número entero.',
            'quality_score.min' => 'La calificación no puede ser menor a 0.',
            'quality_score.max' => 'La calificación no puede ser mayor a 100.',

            'stage.string' => 'La etapa debe ser texto.',
            'stage.max' => 'La etapa no puede tener más de 50 caracteres.',
        ];
    }
}
