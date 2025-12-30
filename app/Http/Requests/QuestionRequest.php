<?php
namespace App\Http\Requests;

use App\Http\Resources\Question\QuestionResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class QuestionRequest extends FormRequest
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
            'category_id'     => 'required|uuid|exists:m_category,id',
            'order_no'        => 'required|integer|min:1',
            'question_text'   => 'required|string|max:255',
            'question_type'   => 'required|string',
        ];
    }

    private function updateRules(): array
    {
        return [
            'category_id'     => 'required|uuid|exists:m_category,id',
            'order_no'        => 'required|integer|min:1',
            'question_text'   => 'required|string|max:255',
            'question_type'   => 'required|string',
        ];
    }
}
