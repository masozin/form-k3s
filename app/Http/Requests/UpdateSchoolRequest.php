<?php
// app/Http/Requests/UpdateSchoolRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSchoolRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('school'));
    }

    public function rules(): array
    {
        return [
            'npsn' => ['required', 'string', 'max:20', Rule::unique('schools', 'npsn')->ignore($this->route('school')->id)],
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'level' => ['required', 'string', 'max:10'],
            'accreditation' => ['nullable', 'string', 'max:5'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'principal_nip' => ['nullable', 'string', 'max:30'],
        ];
    }

    public function messages(): array
    {
        return [
            'npsn.unique' => 'NPSN ini sudah dipakai sekolah lain.',
        ];
    }
}