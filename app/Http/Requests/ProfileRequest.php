<?php

namespace App\Http\Requests;

use App\Http\Resources\User\UserResource;
use App\Models\User\UserAdminModel;
use App\Models\User\UserNakesModel;
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
        $id = auth()->id();
        $user = auth()->user();

        return [
            'name' => 'required|string|max:150',
            'username' => 'required|string|max:100|unique:m_user,username,' . $id,
            'password' => 'nullable|string|min:6',
            'no_telepon' => 'nullable|string|max:20',
            'jenis_kelamin' => 'nullable|in:L,P',

            'nik' => [
                function ($attribute, $value, $fail) use ($user) {
                    if ($user->role === 'kader') {
                        return;
                    }

                    if (empty($value)) {
                        $fail('NIK wajib diisi');
                        return;
                    }

                    if (strlen($value) != 16) {
                        $fail('NIK harus 16 digit');
                        return;
                    }

                    $existsAdmin = UserAdminModel::where('nik', $value)
                        ->where('user_id', '!=', $user->id)
                        ->exists();

                    $existsNakes = UserNakesModel::where('nik', $value)
                        ->where('user_id', '!=', $user->id)
                        ->exists();

                    if ($existsAdmin || $existsNakes) {
                        $fail('NIK sudah terdaftar.');
                    }
                }
            ],
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
