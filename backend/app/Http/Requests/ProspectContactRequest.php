<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class ProspectContactRequest extends FormRequest
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
            'type' => [
                $isCreating ? 'required' : 'sometimes',
                Rule::in([
                    'whatsapp',
                    'phone',
                    'email',
                    'facebook',
                    'instagram',
                    'website',
                ]),
            ],

            'label' => [
                'nullable',
                'string',
                'max:255',
            ],

            'value' => [
                $isCreating ? 'required' : 'sometimes',
                'string',
                'max:255',
            ],

            'is_primary' => [
                'nullable',
                'boolean',
            ],

            'is_verified' => [
                'nullable',
                'boolean',
            ],

            'source_url' => [
                'nullable',
                'url',
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
            'type.required' => 'El tipo de contacto es obligatorio.',
            'type.in' => 'El tipo de contacto no es válido.',

            'value.required' => 'El dato de contacto es obligatorio.',

            'is_primary.boolean' => 'El campo principal debe ser verdadero o falso.',
            'is_verified.boolean' => 'El campo verificado debe ser verdadero o falso.',

            'source_url.url' => 'La URL de origen no tiene un formato válido.',
        ];
    }
}
