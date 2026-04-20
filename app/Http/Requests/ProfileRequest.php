<?php

namespace App\Http\Requests;

use App\Http\Resources\User\UserResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('id') ?? auth()->id();

        return [
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:100|unique:m_user,username,' . $id,
            'password' => 'nullable|string|min:6',

            'no_telepon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi',
            'username.required' => 'Username wajib diisi',
            'username.unique' => 'Username sudah digunakan',
            'password.min' => 'Password minimal 6 karakter',
            'jenis_kelamin.in' => 'Jenis kelamin tidak valid',
        ];
    }
}