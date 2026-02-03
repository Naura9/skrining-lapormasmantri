<?php
namespace App\Http\Requests;

use App\Http\Resources\Kelurahan\KelurahanResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class KelurahanRequest extends FormRequest
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
            'nama_kelurahan' => 'required|max:100',
        ];
    }

    private function updateRules(): array
    {
        return [
            'nama_kelurahan' => 'required|max:100',
        ];
    }

    public function messages(): array
    {
        return [
            'nama_kelurahan.required' => 'Nama kelurahan wajib diisi.',
        ];
    }
}
