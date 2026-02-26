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
            'no_urut'           => 'nullable|integer|min:1',
            'pertanyaan'        => 'required|string',
            'keterangan'        => 'nullable|string',
            'is_required'       => 'nullable|boolean',
            'jenis_jawaban'     => 'required|string|max:50',
            'opsi_jawaban'      => 'nullable|array',
            'opsi_jawaban.*'    => 'nullable',
        ];
    }

    private function updateRules(): array
    {
        return [
            'section_id'        => 'required|uuid|exists:m_section,id',
            'no_urut'           => 'nullable|integer|min:1',
            'pertanyaan'        => 'required|string',
            'keterangan'        => 'nullable|string',
            'is_required'       => 'nullable|boolean',
            'jenis_jawaban'     => 'required|string|max:50',
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

            'no_urut.integer'  => 'Nomor urut harus berupa angka.',
            'no_urut.min'      => 'Nomor urut minimal bernilai 1.',

            'pertanyaan.required' => 'Pertanyaan wajib diisi.',
            'pertanyaan.string'   => 'Pertanyaan harus berupa teks.',

            'keterangan.string' => 'Keterangan harus berupa teks.',
            
            'is_required.boolean' => 'Status wajib tidak valid.',

            'jenis_jawaban.required' => 'Jenis jawaban wajib dipilih.',
            'jenis_jawaban.string'   => 'Jenis jawaban harus berupa teks.',
            'jenis_jawaban.max'      => 'Jenis jawaban maksimal 50 karakter.',

            'opsi_jawaban.array' => 'Opsi jawaban harus berupa array.',
        ];
    }
}
