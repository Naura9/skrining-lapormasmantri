<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class FamilyMemberRequest extends FormRequest
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
            'family_id'           => 'required|uuid|exists:m_family,id',
            'full_name'           => 'required|string|max:100',
            'national_id_number'  => 'required|string|max:16|unique:m_family_member,national_id_number',
            'place_of_birth'      => 'required|string|max:100',
            'date_of_birth'       => 'required|date',
            'gender'              => 'required|in:male,female',
            'relationship'        => 'required|string|max:50',
            'marital_status'      => 'required|string|max:50',
            'last_education'      => 'string|max:50',
            'occupation'          => 'string|max:100',
        ];
    }

    private function updateRules(): array
    {
        return [
            'id'                  => 'required|uuid|exists:m_family_member,id',
            'family_id'           => 'required|uuid|exists:m_family,id',
            'full_name'           => 'required|string|max:100',
            'national_id_number'  => 'required|string|max:16|unique:m_family_member,national_id_number,' . $this->id,
            'place_of_birth'      => 'required|string|max:100',
            'date_of_birth'       => 'required|date',
            'gender'              => 'required|in:male,female',
            'relationship'        => 'required|string|max:50',
            'marital_status'      => 'required|string|max:50',
            'last_education'      => 'string|max:50',
            'occupation'          => 'string|max:100',
        ];
    }
}
