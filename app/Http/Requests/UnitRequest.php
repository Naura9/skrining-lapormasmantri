<?php

namespace App\Http\Requests;

use App\Http\Resources\Jawaban\UnitResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UnitRequest extends FormRequest
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
        return [
            'unit_rumah_id'   => 'required|uuid|exists:m_unit_rumah,id',
            'no_kk'           => 'required|digits:16|unique:m_keluarga,no_kk',
            'kepala_keluarga' => 'required|string|max:150',
        ];
    }

    private function updateRules(): array
    {
        return [
            'unit_rumah_id'   => 'required|uuid|exists:m_unit_rumah,id',
            'no_kk'           => 'required|digits:16|exists:m_keluarga,no_kk',
            'kepala_keluarga' => 'required|string|max:150',
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

            'kepala_keluarga.required' => 'Nama kepala keluarga wajib diisi.',
            'kepala_keluarga.string'   => 'Nama kepala keluarga harus berupa teks.',
            'kepala_keluarga.max'      => 'Nama kepala keluarga maksimal 150 karakter.',
        ];
    }
}
