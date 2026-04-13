<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class GoogleRedirectRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return !$this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'role_selected' => [
                'required',
                'string',
                'exists:roles,slug',
            ],
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages(): array
    {
        return [
            'role_selected.required' => 'Silakan pilih role terlebih dahulu.',
            'role_selected.string'   => 'Role harus berupa teks.',
            'role_selected.exists'   => 'Role yang dipilih tidak valid.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'role_selected' => trim($this->role_selected),
        ]);
    }
}
