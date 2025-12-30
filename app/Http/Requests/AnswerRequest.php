<?php

namespace App\Http\Requests;

use App\Http\Resources\Answer\AnswerResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class AnswerRequest extends FormRequest
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
            'question_id'       => 'required|uuid|exists:m_question,id',
            'family_id'         => 'required|uuid|exists:m_family,id',
            'family_member_id'  => 'required|uuid|exists:m_family_member,id',
            'screening_id'      => 'required|uuid|exists:t_screening,id',
            'answer_value'      => 'required|string',
        ];
    }

    private function updateRules(): array
    {
        return [
            'id'                => 'required|uuid|exists:t_answer,id',
            'question_id'       => 'required|uuid|exists:m_question,id',
            'family_id'         => 'required|uuid|exists:m_family,id',
            'family_member_id'  => 'required|uuid|exists:m_family_member,id',
            'screening_id'      => 'required|uuid|exists:t_screening,id',
            'answer_value'      => 'required|string',
        ];
    }
}
