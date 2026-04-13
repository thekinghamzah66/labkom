<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only guests (unauthenticated users) should be able to login.
     */
    public function authorize(): bool
    {
        // The RedirectIfAuthenticated middleware already handles authenticated users,
        // but we add this for defense-in-depth
        return !$this->user();
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'username'       => [
                'required',
                'string',
                'max:100',
            ],
            'password'       => [
                'required',
                'string',
                'min:6', // Add minimum length for security
            ],
            'role_selected'  => [
                'required',
                'string',
                'exists:roles,slug', // Validate role exists and is valid
            ],
        ];
    }

    /**
     * Custom error messages to display to the user.
     */
    public function messages(): array
    {
        return [
            'username.required'        => 'Username wajib diisi.',
            'username.string'          => 'Username harus berupa teks.',
            'username.max'             => 'Username tidak boleh lebih dari 100 karakter.',

            'password.required'        => 'Password wajib diisi.',
            'password.string'          => 'Password harus berupa teks.',
            'password.min'             => 'Password minimal 6 karakter.',

            'role_selected.required'   => 'Terjadi kesalahan sistem, role tidak terdeteksi.',
            'role_selected.string'     => 'Role harus berupa teks.',
            'role_selected.exists'     => 'Role tidak valid.',
        ];
    }

    /**
     * Prepare the data for validation.
     * Trim whitespace from inputs to prevent bypass.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'username'      => trim($this->username),
            'role_selected' => trim($this->role_selected),
        ]);
    }
}
