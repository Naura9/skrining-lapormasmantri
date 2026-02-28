<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IdentitasKeluargaRequest extends FormRequest
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
            'kelurahan_id' => 'required|exists:m_kelurahan,id',
            'posyandu_id'  => 'required|exists:m_posyandu,id',
            'alamat'       => 'required|string|max:255',
            'rt'           => 'required|string|max:5',
            'rw'           => 'required|string|max:5',

            'keluarga' => 'required|array|min:1',

            'keluarga.*.no_kk' => [
                'required',
                'string',
                'max:20',
                'distinct', 
                Rule::unique('m_keluarga', 'no_kk') 
            ],

            'keluarga.*.is_luar_wilayah' => 'required|boolean',
            'keluarga.*.alamat_ktp' => 'nullable|string',
            'keluarga.*.rt_ktp' => 'nullable|string|max:5',
            'keluarga.*.rw_ktp' => 'nullable|string|max:5',
            'keluarga.*.no_telepon' => 'nullable|string|max:20',

            'keluarga.*.nik_kepala_keluarga' => [
                'required',
                'string',
                'max:20',
                'distinct', 
                Rule::unique('m_keluarga', 'nik_kepala_keluarga') 
            ],

            'keluarga.*.nama_kepala_keluarga' => 'required|string|max:150',
        ];
    }

    private function updateRules(): array
    {
        return $this->createRules();
    }

    public function messages(): array
    {
        return [
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'kelurahan_id.exists'   => 'Kelurahan tidak ditemukan.',

            'posyandu_id.required' => 'Posyandu wajib dipilih.',
            'posyandu_id.exists'   => 'Posyandu tidak ditemukan.',

            'alamat.required' => 'Alamat wajib diisi.',
            'rt.required' => 'RT wajib diisi.',
            'rw.required' => 'RW wajib diisi.',

            'keluarga.required' => 'Minimal harus ada 1 keluarga.',
            'keluarga.*.no_kk.required' => 'No KK wajib diisi.',
            'keluarga.*.no_kk.distinct' => 'No KK tidak boleh duplikat dalam satu input.',
            'keluarga.*.no_kk.unique'   => 'No KK sudah terdaftar.',

            'keluarga.*.nik_kepala_keluarga.distinct' => 'NIK tidak boleh duplikat dalam satu input.',
            'keluarga.*.nik_kepala_keluarga.unique'   => 'NIK sudah terdaftar.',

            'keluarga.*.nik_kepala_keluarga.required' => 'NIK kepala keluarga wajib diisi.',
            'keluarga.*.nama_kepala_keluarga.required' => 'Nama kepala keluarga wajib diisi.',
        ];
    }
}
