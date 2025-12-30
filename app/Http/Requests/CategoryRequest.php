<?php
namespace App\Http\Requests;

use App\Http\Resources\Category\CategoryResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
            'category_name' => 'required|max:100',
        ];
    }

    private function updateRules(): array
    {
        return [
            'category_name' => 'required|max:100',
        ];
    }
}
