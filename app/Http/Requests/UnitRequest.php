<?php

namespace App\Http\Requests;

use App\Http\Resources\Warga\UnitResource;
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
            'kelurahan_id' => 'required|uuid|exists:m_kelurahan,id',
            'posyandu_id'  => 'required|uuid|exists:m_posyandu,id',
            'alamat' => 'required|string|max:255',
            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
        ];
    }

    private function updateRules(): array
    {
        return [
            'kelurahan_id' => 'required|uuid|exists:m_kelurahan,id',
            'posyandu_id'  => 'required|uuid|exists:m_posyandu,id',

            'alamat' => 'required|string|max:255',

            'rt' => 'required|string|max:3',
            'rw' => 'required|string|max:3',
        ];
    }

    public function messages(): array
    {
        return [
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'kelurahan_id.uuid'     => 'Format kelurahan tidak valid.',
            'kelurahan_id.exists'   => 'Kelurahan tidak ditemukan.',

            'posyandu_id.required' => 'Posyandu wajib dipilih.',
            'posyandu_id.uuid'     => 'Format posyandu tidak valid.',
            'posyandu_id.exists'   => 'Posyandu tidak ditemukan.',

            'alamat.required' => 'Alamat wajib diisi.',
            'alamat.string'   => 'Alamat harus berupa teks.',
            'alamat.max'      => 'Alamat maksimal 255 karakter.',

            'rt.required' => 'RT wajib diisi.',
            'rt.string'   => 'RT harus berupa teks.',
            'rt.max'      => 'RT maksimal 3 karakter.',

            'rw.required' => 'RW wajib diisi.',
            'rw.string'   => 'RW harus berupa teks.',
            'rw.max'      => 'RW maksimal 3 karakter.',
        ];
    }
}
