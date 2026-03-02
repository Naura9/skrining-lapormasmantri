<?php

namespace App\Http\Requests;

use App\Http\Resources\Warga\KeluargaResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KeluargaRequest extends FormRequest
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
            'unit_rumah_id' => 'required|uuid|exists:m_unit_rumah,id',
            'no_kk' => 'required|digits:16|unique:m_keluarga,no_kk',
            'nik_kepala_keluarga' => 'required|digits:16|unique:m_anggota_keluarga,nik',
            'nama_kepala_keluarga' => 'required|string|max:150',
            'no_telepon' => 'nullable|string|max:20'
        ];
    }

    private function updateRules($id): array
    {
        return [
            'unit_rumah_id' => 'required|uuid|exists:m_unit_rumah,id',
            'no_kk' => [
                'required',
                'digits:16',
                Rule::unique('m_keluarga', 'no_kk')->ignore($id),
            ],
            'nik_kepala_keluarga' => 'required|digits:16',
            'nama_kepala_keluarga' => 'required|string|max:150',
        ];
    }

    public function messages(): array
    {
        return [
            'unit_rumah_id.required' => 'Unit rumah wajib dipilih.',
            'unit_rumah_id.uuid'     => 'Format unit rumah tidak valid.',
            'unit_rumah_id.exists'   => 'Unit rumah tidak ditemukan.',

            'no_kk.required' => 'Nomor KK wajib diisi.',
            'no_kk.digits'   => 'Nomor KK harus terdiri dari 16 digit.',
            'no_kk.unique'   => 'Nomor KK sudah terdaftar.',

            'nik_kepala_keluarga.required' => 'NIK kepala keluarga wajib diisi.',
            'nik_kepala_keluarga.digits'   => 'NIK kepala keluarga harus terdiri dari 16 digit.',
            'nik_kepala_keluarga.unique'   => 'NIK kepala keluarga sudah terdaftar.',

            'nama_kepala_keluarga.required' => 'Nama kepala keluarga wajib diisi.',
            'nama_kepala_keluarga.string'   => 'Nama kepala keluarga harus berupa teks.',
            'nama_kepala_keluarga.max'      => 'Nama kepala keluarga maksimal 150 karakter.',
        ];
    }
}
