<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SectionRequest extends FormRequest
{
    public $validator = null;

    protected function failedValidation(Validator $validator)
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
            'kategori_id'   => 'required|uuid|exists:m_kategori,id',
            'judul_section' => 'required|string|max:150',
            'no_urut'       => 'required|integer|min:1',
        ];
    }

    private function updateRules(): array
    {
        return [
            'kategori_id'   => 'required|uuid|exists:m_kategori,id',
            'judul_section' => 'required|string|max:150',
            'no_urut'       => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'kategori_id.uuid'     => 'Kategori tidak valid.',
            'kategori_id.exists'   => 'Kategori tidak ditemukan.',

            'judul_section.required' => 'Judul section wajib diisi.',
            'judul_section.string'   => 'Judul section harus berupa teks.',
            'judul_section.max'      => 'Judul section maksimal 150 karakter.',

            'no_urut.required' => 'Nomor urut wajib diisi.',
            'no_urut.integer'  => 'Nomor urut harus berupa angka.',
            'no_urut.min'      => 'Nomor urut minimal bernilai 1.',
        ];
    }
}
