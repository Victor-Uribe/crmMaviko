<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ProspectIntegrationRequest extends FormRequest
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
            'external_id' => [
                'required',
                'string',
                'max:255',
            ],

            'source' => [
                'required',
                'string',
                'max:100',
            ],

            'metadata' => [
                'nullable',
                'array',
            ],

            'prospects' => [
                'required',
                'array',
                'min:1',
                'max:100',
            ],

            'prospects.*.business_name' => [
                'required',
                'string',
                'max:255',
            ],

            'prospects.*.category' => [
                'nullable',
                'string',
                'max:255',
            ],

            'prospects.*.description' => [
                'nullable',
                'string',
            ],

            'prospects.*.country' => [
                'nullable',
                'string',
                'max:255',
            ],

            'prospects.*.state' => [
                'nullable',
                'string',
                'max:255',
            ],

            'prospects.*.city' => [
                'nullable',
                'string',
                'max:255',
            ],

            'prospects.*.address' => [
                'nullable',
                'string',
            ],

            'prospects.*.source' => [
                'nullable',
                'string',
                'max:255',
            ],

            'prospects.*.source_url' => [
                'nullable',
                'url',
            ],

            'prospects.*.opportunity' => [
                'nullable',
                'string',
            ],

            'prospects.*.quality_score' => [
                'nullable',
                'integer',
                'min:0',
                'max:100',
            ],

            'prospects.*.contacts' => [
                'nullable',
                'array',
            ],

            'prospects.*.contacts.*.type' => [
                'required',
                'string',
            ],

            'prospects.*.contacts.*.value' => [
                'required',
                'string',
            ],

            'prospects.*.opportunities' => [
                'nullable',
                'array',
            ],

            'prospects.*.outreach' => [
                'nullable',
                'array',
            ],
        ];
    }
}
