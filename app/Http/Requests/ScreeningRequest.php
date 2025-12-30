<?php

namespace App\Http\Requests;

use App\Http\Resources\Screening\ScreeningResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class ScreeningRequest extends FormRequest
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
            'family_id'      => 'required|uuid|exists:m_family,id',
            'user_id'        => 'required|uuid|exists:m_user,id',
            'screening_date' => 'required|date',
        ];
    }

    private function updateRules(): array
    {
        return [
            'id'             => 'required|uuid|exists:t_screening,id',
            'family_id'      => 'required|uuid|exists:m_family,id',
            'user_id'        => 'required|uuid|exists:m_user,id',
            'screening_date' => 'required|date',
        ];
    }
}
