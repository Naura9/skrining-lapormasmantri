<?php

namespace App\Http\Requests;

use App\Http\Resources\Kategori\KategoriResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class KategoriRequest extends FormRequest
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
            'nama_kategori'   => 'required|string|max:100',
            'target_skrining' => 'required|in:nik,kk',
        ];
    }

    private function updateRules(): array
    {
        return [
            'nama_kategori'   => 'required|string|max:100',
            'target_skrining' => 'required|in:nik,kk',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kategori.required' => 'Nama kategori wajib diisi.',
            'nama_kategori.string'   => 'Nama kategori harus berupa teks.',
            'nama_kategori.max'      => 'Nama kategori maksimal 100 karakter.',

            'target_skrining.required' => 'Target skrining wajib dipilih.',
            'target_skrining.in'       => 'Target skrining harus NIK atau KK.',
        ];
    }
}
