<?php

namespace App\Http\Requests;

use App\Http\Requests\Concerns\HasImageUploads;
use Illuminate\Foundation\Http\FormRequest;

class ProfileUpdateRequest extends FormRequest
{
    use HasImageUploads;

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $common_rules = [
            'data.*.image' => $this->uploadedImageRules(),
        ];

        return array_merge($common_rules, $this->sectionRules());
    }

    public function sectionRules(): array
    {
        $rulesMethod = $this->route()->parameter('section') . 'Rules';

        return method_exists($this, $rulesMethod) ? $this->$rulesMethod() : [];
    }

    public function informationRules(): array
    {
        return [
            'full_name' => 'required|string',
            'public' => 'required|boolean',
            'data.*.data.email' => 'nullable|email',
            'data.*.data.url' => 'nullable|url',
            'data.*.data.secondary_url' => 'nullable|url',
            'data.*.data.tertiary_url' => 'nullable|url',
            'data.*.data.orc_id' => 'nullable|regex:/^[0-9]{4}-[0-9]{4}-[0-9]{4}-[0-9]{3}[0-9X]$/',
            'data.*.data.orc_id_managed' => 'required|boolean',
            'data.*.data.fancy_header' => 'required|boolean',
            'data.*.data.fancy_header_right' => 'required|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'data.*.image.max' => $this->uploadedImageMessages('max'),
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'full_name' => 'Display Name',
            'data.*.data.email' => 'Email address',
            'data.*.data.url' => 'Primary URL',
            'data.*.data.secondary_url' => 'Secondary URL',
            'data.*.data.tertiary_url' => 'Tertiary URL',
            'data.*.data.orc_id' => 'ORCID',
        ];
    }
}
