<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Models\AnggotaKeluargaModel;

class IdentitasAnggotaRequest extends FormRequest
{
    public $validator = null;

    public function authorize(): bool
    {
        return true;
    }

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

    private function createRules(): array
    {
        return [
            'keluarga_id' => 'required|exists:m_keluarga,id',

            'nik' => [
                'required',
                'digits:16',
                Rule::unique('m_anggota_keluarga', 'nik'),
            ],

            'nama' => 'required|string|max:150',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'hubungan_keluarga' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value === 'Kepala Keluarga') {
                        $keluargaId = $this->input('keluarga_id');
                        $exists = AnggotaKeluargaModel::where('keluarga_id', $keluargaId)
                            ->where('hubungan_keluarga', 'Kepala Keluarga')
                            ->exists();
                        if ($exists) {
                            $fail('Hanya boleh ada 1 Kepala Keluarga per KK.');
                        }
                    }
                }
            ],
            'status_perkawinan' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'pekerjaan' => 'required|string',
        ];
    }

    private function updateRules(): array
    {
        return [
            'keluarga_id' => 'required|exists:m_keluarga,id',

            'nik' => [
                'required',
                'digits:16',
                function ($attribute, $value, $fail) {
                    $id = $this->input('id');
                    $query = AnggotaKeluargaModel::where('nik', $value);
                    if ($id) {
                        $query->where('id', '!=', $id);
                    }
                    if ($query->exists()) {
                        $fail('NIK sudah terdaftar.');
                    }
                }
            ],

            'nama' => 'required|string|max:150',
            'tempat_lahir' => 'required|string|max:100',
            'tanggal_lahir' => 'required|date',
            'jenis_kelamin' => 'required|in:L,P',
            'hubungan_keluarga' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if ($value === 'Kepala Keluarga') {
                        $keluargaId = $this->input('keluarga_id');
                        $id = $this->route('id');
                        $exists = AnggotaKeluargaModel::where('keluarga_id', $keluargaId)
                            ->where('hubungan_keluarga', 'Kepala Keluarga')
                            ->where('id', '!=', $id)
                            ->exists();
                        if ($exists) {
                            $fail('Hanya boleh ada 1 Kepala Keluarga per KK.');
                        }
                    }
                }
            ],

            'status_perkawinan' => 'required|string',
            'pendidikan_terakhir' => 'required|string',
            'pekerjaan' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'keluarga_id.required' => 'Kartu Keluarga wajib dipilih.',
            'keluarga_id.exists'   => 'Data Kartu Keluarga tidak ditemukan.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.digits'   => 'NIK harus terdiri dari 16 digit angka.',
            'nik.unique'   => 'NIK sudah terdaftar.',
            'nama.required' => 'Nama lengkap wajib diisi.',
            'nama.string'   => 'Nama lengkap harus berupa teks.',
            'nama.max'      => 'Nama lengkap maksimal 150 karakter.',
            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tempat_lahir.string'   => 'Tempat lahir harus berupa teks.',
            'tempat_lahir.max'      => 'Tempat lahir maksimal 100 karakter.',
            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date'     => 'Format tanggal lahir tidak valid.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin tidak valid.',
            'hubungan_keluarga.required' => 'Hubungan keluarga wajib dipilih.',
            'status_perkawinan.required' => 'Status perkawinan wajib dipilih.',
            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib dipilih.',
            'pekerjaan.required' => 'Pekerjaan wajib dipilih.',
        ];
    }
}