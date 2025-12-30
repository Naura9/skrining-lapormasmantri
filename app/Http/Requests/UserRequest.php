<?php
namespace App\Http\Requests;

use App\Http\Resources\User\UserResource;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            'name' => 'required|max:100',
            'email' => 'required|email|unique:m_user',
            'password' => 'required|min:6',
            'role' => 'required|string|in:admin,kader,nakes',
        ];
    }

    private function updateRules(): array
    {
        return [
            'name' => 'required|max:100',
            'photo' => 'nullable|file|image',
            'email' => 'required|email|unique:m_user,email,' . $this->id,
            'role' => 'string|in:admin,kader,nakes',
        ];
    }
}
