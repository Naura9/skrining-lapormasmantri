<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class TargetRequest extends FormRequest
{
    public $validator = null;

    public function failedValidation(Validator $validator)
    {
        $this->validator = $validator;
    }

    public function authorize(): bool
    {
        return true;
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
            'kelurahan_id' => 'required|uuid|exists:m_kelurahan,id',
            'kategori_id'  => 'required|uuid|exists:m_kategori,id',
            'target'       => 'required|integer|min:0',
        ];
    }

    private function updateRules(): array
    {
        return [
            'kelurahan_id' => 'required|uuid|exists:m_kelurahan,id',
            'kategori_id'  => 'required|uuid|exists:m_kategori,id',
            'target'       => 'required|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'kelurahan_id.required' => 'Kelurahan wajib dipilih.',
            'kelurahan_id.uuid'     => 'Format kelurahan tidak valid.',
            'kelurahan_id.exists'   => 'Kelurahan tidak ditemukan.',

            'kategori_id.required'  => 'Kategori wajib dipilih.',
            'kategori_id.uuid'      => 'Format kategori tidak valid.',
            'kategori_id.exists'    => 'Kategori tidak ditemukan.',

            'target.required' => 'Target skrining wajib diisi.',
            'target.integer'   => 'Target harus berupa angka.',
            'target.min'      => 'Target tidak boleh kurang dari 0.',
        ];
    }
}