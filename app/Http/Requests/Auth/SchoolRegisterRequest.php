<?php
// app/Http/Requests/Auth/SchoolRegisterRequest.php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class SchoolRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'school_npsn' => ['required', 'string', 'max:20', 'unique:schools,npsn'],
            'school_name' => ['required', 'string', 'max:255'],
            'school_address' => ['nullable', 'string'],
            'school_level' => ['required', 'string', 'max:10'],
            'school_accreditation' => ['nullable', 'string', 'max:5'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'principal_nip' => ['nullable', 'string', 'max:30'],

            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    public function messages(): array
    {
        return [
            'school_npsn.unique' => 'NPSN ini sudah terdaftar di sistem.',
        ];
    }
}
