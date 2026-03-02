<?php

namespace App\Http\Requests;

use App\Http\Resources\Warga\KeluargaResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AnggotaKeluargaRequest extends FormRequest
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

        if ($this->isMethod('put')) {
            return $this->updateRules($this->id);
        }

        return [];
    }

    public function authorize(): bool
    {
        return true;
    }

    private function createRules(): array
    {
        return [
            'keluarga_id'        => 'required|uuid|exists:m_keluarga,id',
            'nama'               => 'required|string|max:150',
            'nik'                => 'required|digits:16|unique:m_anggota_keluarga,nik',
            'tempat_lahir'       => 'required|string|max:100',
            'tanggal_lahir'      => 'required|date',
            'jenis_kelamin'      => 'required|in:L,P',
            'no_kk_asal'         => 'nullable|digits:16',
            'hubungan_keluarga'  => 'required|string|max:50',
            'status_perkawinan'  => 'required|string|max:30',
            'pendidikan_terakhir' => 'required|string|max:50',
            'pekerjaan'          => 'nullable|string|max:100',
        ];
    }

    private function updateRules($id): array
    {
        return [
            'keluarga_id' => 'required|uuid|exists:m_keluarga,id',
            'nama'        => 'required|string|max:150',

            'nik' => [
                'required',
                'digits:16',
                Rule::unique('m_anggota_keluarga', 'nik')->ignore($id),
            ],

            'tempat_lahir'        => 'required|string|max:100',
            'tanggal_lahir'       => 'required|date',
            'jenis_kelamin'       => 'required|in:L,P',
            'no_kk_asal'          => 'nullable|digits:16',
            'hubungan_keluarga'   => 'required|string|max:50',
            'status_perkawinan'   => 'required|string|max:30',
            'pendidikan_terakhir' => 'required|string|max:50',
            'pekerjaan'           => 'nullable|string|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'keluarga_id.required' => 'Keluarga wajib dipilih.',
            'keluarga_id.uuid'     => 'Format keluarga tidak valid.',
            'keluarga_id.exists'   => 'Data keluarga tidak ditemukan.',

            'nama.required' => 'Nama wajib diisi.',
            'nama.string'   => 'Nama harus berupa teks.',
            'nama.max'      => 'Nama maksimal 150 karakter.',

            'nik.required' => 'NIK wajib diisi.',
            'nik.digits'   => 'NIK harus terdiri dari 16 digit.',
            'nik.unique'   => 'NIK sudah terdaftar.',

            'tempat_lahir.required' => 'Tempat lahir wajib diisi.',
            'tempat_lahir.string'   => 'Tempat lahir harus berupa teks.',
            'tempat_lahir.max'      => 'Tempat lahir maksimal 100 karakter.',

            'tanggal_lahir.required' => 'Tanggal lahir wajib diisi.',
            'tanggal_lahir.date'     => 'Format tanggal lahir tidak valid.',

            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'jenis_kelamin.in'       => 'Jenis kelamin harus L atau P.',

            'no_kk_asal.digits' => 'Nomor KK asal harus terdiri dari 16 digit.',

            'hubungan_keluarga.required' => 'Hubungan keluarga wajib diisi.',
            'hubungan_keluarga.max'      => 'Hubungan keluarga maksimal 50 karakter.',

            'status_perkawinan.required' => 'Status perkawinan wajib diisi.',
            'status_perkawinan.max'      => 'Status perkawinan maksimal 30 karakter.',

            'pendidikan_terakhir.required' => 'Pendidikan terakhir wajib diisi.',
            'pendidikan_terakhir.max'      => 'Pendidikan terakhir maksimal 50 karakter.',

            'pekerjaan.max' => 'Pekerjaan maksimal 100 karakter.',
        ];
    }
}
