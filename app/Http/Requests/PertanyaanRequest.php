<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class PertanyaanRequest extends FormRequest
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
            'section_id'        => 'required|uuid|exists:m_section,id',
            'no_urut'           => 'required|integer|min:1',
            'pertanyaan'        => 'required|string',
            'jenis_pertanyaan'  => 'required|string|max:50',
            'opsi_jawaban'      => 'nullable|array',
            'opsi_jawaban.*'    => 'nullable',
        ];
    }

    private function updateRules(): array
    {
        return [
            'section_id'        => 'required|uuid|exists:m_section,id',
            'no_urut'           => 'required|integer|min:1',
            'pertanyaan'        => 'required|string',
            'jenis_pertanyaan'  => 'required|string|max:50',
            'opsi_jawaban'      => 'nullable|array',
            'opsi_jawaban.*'    => 'nullable',
        ];
    }

    public function messages(): array
    {
        return [
            'section_id.required' => 'Section wajib dipilih.',
            'section_id.uuid'     => 'Section tidak valid.',
            'section_id.exists'   => 'Section tidak ditemukan.',

            'no_urut.required' => 'Nomor urut wajib diisi.',
            'no_urut.integer'  => 'Nomor urut harus berupa angka.',
            'no_urut.min'      => 'Nomor urut minimal bernilai 1.',

            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'pertanyaan.string'   => 'Pertanyaan harus berupa teks.',

            'jenis_pertanyaan.required' => 'Jenis pertanyaan wajib diisi.',
            'jenis_pertanyaan.string'   => 'Jenis pertanyaan harus berupa teks.',
            'jenis_pertanyaan.max'      => 'Jenis pertanyaan maksimal 50 karakter.',

            'opsi_jawaban.array' => 'Opsi jawaban harus berupa array.',
        ];
    }
}
