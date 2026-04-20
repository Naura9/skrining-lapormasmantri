<?php

namespace App\Http\Requests;

use App\Http\Resources\Skrining\SkriningResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class SkriningRequest extends FormRequest
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
            'keluarga_id' => 'required|uuid|exists:m_keluarga,id',
            'tanggal_skrining' => 'required|date',

            'jawaban' => 'required|array|min:1',

            'jawaban.*.pertanyaan_id' => 'required|uuid|exists:m_pertanyaan,id',
            'jawaban.*.anggota_keluarga_id' => 'nullable|uuid|exists:m_anggota_keluarga,id',
            'jawaban.*.value_jawaban' => 'nullable|string',
        ];
    }

    private function updateRules(): array
    {
        return $this->createRules();
    }

    public function messages(): array
    {
        return [
            'keluarga_id.required' => 'Keluarga wajib dipilih.',
            'keluarga_id.uuid'     => 'Format keluarga tidak valid.',
            'keluarga_id.exists'   => 'Data keluarga tidak ditemukan.',

            'user_id.required' => 'User wajib dipilih.',
            'user_id.uuid'     => 'Format user tidak valid.',
            'user_id.exists'   => 'User tidak ditemukan.',
            
            'tanggal_skrining.required' => 'Tanggal skrining wajib diisi.',
            'tanggal_skrining.date'     => 'Format tanggal tidak valid.',

            'jawaban.required' => 'Jawaban wajib diisi.',
            'jawaban.array'    => 'Format jawaban tidak valid.',
            'jawaban.min'      => 'Minimal harus ada 1 jawaban.',

            'jawaban.*.pertanyaan_id.required' => 'Pertanyaan wajib dipilih.',
            'jawaban.*.pertanyaan_id.uuid'     => 'Format pertanyaan tidak valid.',
            'jawaban.*.pertanyaan_id.exists'   => 'Pertanyaan tidak ditemukan.',

            'jawaban.*.anggota_keluarga_id.required' => 'Anggota keluarga wajib dipilih.',
            'jawaban.*.anggota_keluarga_id.uuid'     => 'Format anggota keluarga tidak valid.',
            'jawaban.*.anggota_keluarga_id.exists'   => 'Anggota keluarga tidak ditemukan.',

            'jawaban.*.value_jawaban.string' => 'Jawaban harus berupa teks.',
        ];
    }
}
