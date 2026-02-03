<?php

namespace App\Http\Requests;

use App\Http\Resources\User\UserResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    public $validator = null;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function rules(): array
    {
        if ($this->isMethod('post')) {
            return $this->createRules();
        }

        return $this->updateRules();
    }

    public function authorize(): bool
    {
        return true;
    }

    private function createRules(): array
    {
        $rules = [
            'name' => 'required|max:100',
            'username' => 'required|unique:m_user,username',
            'password' => 'required|min:6',
            'role' => 'required|in:admin,kader,nakes',
        ];

        if ($this->role === 'admin') {
            $rules['nik'] = 'required|digits:16|unique:m_user_admin,nik';
            $rules['no_telepon'] = 'required|max:15';
            $rules['jenis_kelamin'] = 'required|in:L,P';
        }

        if ($this->role === 'nakes') {
            $rules['kelurahan_id'] = 'required|uuid|exists:m_kelurahan,id';
            $rules['nik'] = 'required|digits:16|unique:m_user_nakes,nik';
            $rules['no_telepon'] = 'required|max:15';
            $rules['jenis_kelamin'] = 'required|in:L,P';
        }

        if ($this->role === 'kader') {
            $rules['posyandu_id'] = 'required|uuid|exists:m_posyandu,id';
            $rules['no_telepon'] = 'required|max:15';
            $rules['jenis_kelamin'] = 'required|in:L,P';
            $rules['status'] = 'required|in:aktif,nonaktif';
        }

        return $rules;
    }

    private function updateRules(): array
    {
        $rules = [
            'name' => 'required|max:100',
            'username' => 'required|unique:m_user,username,' . $this->id,
            'role' => 'string|in:admin,kader,nakes',
        ];

        if ($this->role === 'admin') {
            $rules['nik'] = 'required|digits:16|unique:m_user_admin,nik,' . $this->id . ',user_id';
            $rules['no_telepon'] = 'required|max:15';
            $rules['jenis_kelamin'] = 'required|in:L,P';
        }

        if ($this->role === 'nakes') {
            $rules['kelurahan_id'] = 'required|uuid|exists:m_kelurahan,id';
            $rules['nik'] = 'required|digits:16|unique:m_user_nakes,nik,' . $this->id . ',user_id';
            $rules['no_telepon'] = 'required|max:15';
            $rules['jenis_kelamin'] = 'required|in:L,P';
        }

        if ($this->role === 'kader') {
            $rules['posyandu_id'] = 'required|uuid|exists:m_posyandu,id';
            $rules['no_telepon'] = 'required|max:15';
            $rules['jenis_kelamin'] = 'required|in:L,P';
            $rules['status'] = 'required|in:aktif,nonaktif';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'name.max' => 'Nama lengkap maksimal 100 karakter.',
            'username.required' => 'Username wajib diisi.',
            'username.unique' => 'Username sudah digunakan.',
            'password.required' => 'Password wajib diisi.',
            'password.min' => 'Password minimal 6 karakter.',
            'role.required' => 'Role wajib dipilih.',
            'role.in' => 'Role harus salah satu dari admin, kader, atau nakes.',

            // admin
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits' => 'NIK harus 16 digit.',
            'nik.unique' => 'NIK sudah digunakan.',
            'no_telepon.required' => 'Nomor telepon wajib diisi.',
            'no_telepon.max' => 'Nomor telepon maksimal 15 karakter.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in' => 'Jenis kelamin harus L (Laki-laki) atau P (Perempuan).',

            // nakes
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'kelurahan_id.uuid' => 'Kelurahan tidak valid.',
            'kelurahan_id.exists' => 'Kelurahan tidak ditemukan.',

            // kader
            'posyandu_id.required' => 'Posyandu wajib dipilih.',
            'posyandu_id.uuid' => 'Posyandu tidak valid.',
            'posyandu_id.exists' => 'Posyandu tidak ditemukan.',
            'status.required' => 'Status wajib dipilih.',
            'status.in' => 'Status harus aktif atau nonaktif.',
        ];
    }
}
