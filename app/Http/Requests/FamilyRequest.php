<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class FamilyRequest extends FormRequest
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
            'family_card_number' => 'required|string|max:20|unique:m_family,family_card_number',
            'head_of_family'     => 'required|string|max:100',
            'address'            => 'required|string|max:255',
            'neighborhood_rt'    => 'required|string|max:5',
            'neighborhood_rw'    => 'required|string|max:5',
            'urban_village'      => 'required|string|max:100',
            'posyandu'       => 'nullable|string|max:100',
        ];
    }

    private function updateRules(): array
    {
        return [
            'id'                 => 'required|uuid|exists:m_family,id',
            'family_card_number' => 'required|string|max:20|unique:m_family,family_card_number,' . $this->id,
            'head_of_family'     => 'required|string|max:100',
            'address'            => 'required|string|max:255',
            'neighborhood_rt'    => 'required|string|max:5',
            'neighborhood_rw'    => 'required|string|max:5',
            'urban_village'      => 'required|string|max:100',
            'posyandu'       => 'nullable|string|max:100',
        ];
    }
}
