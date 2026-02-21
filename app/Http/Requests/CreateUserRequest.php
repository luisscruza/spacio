<?php

namespace App\Http\Requests;

use Spacio\Framework\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:120',
            'email' => 'nullable|email|unique:users,email',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a name.',
            'name.min' => 'Name must be at least 2 characters.',
            'email.email' => 'Email must be a valid address.',
            'email.unique' => 'This email is already taken.',
        ];
    }
}
