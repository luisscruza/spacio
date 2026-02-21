<?php

namespace App\Components\Users;

use App\Entities\User;
use Spacio\Framework\Components\Component;
use Spacio\Framework\Validation\ValidationException;
use Spacio\Framework\Validation\Validator;

class CreateFormComponent extends Component
{
    public ?string $name = null;

    public ?string $email = null;

    public bool $success = false;

    public ?int $userId = null;

    public array $errors = [];

    public function view(): string
    {
        return 'users.create-form';
    }

    public function save(array $data = []): void
    {
        $this->success = false;
        $this->errors = [];
        $this->name = $data['name'] ?? null;
        $this->email = $data['email'] ?? null;

        $validator = new Validator;

        try {
            $validated = $validator->validate(
                $data,
                [
                    'name' => 'required|string|min:2|max:120',
                    'email' => 'nullable|email|unique:users,email',
                ],
                [
                    'name.required' => 'Please enter a name.',
                    'name.min' => 'Name must be at least 2 characters.',
                    'email.email' => 'Email must be a valid address.',
                    'email.unique' => 'This email is already taken.',
                ]
            );
        } catch (ValidationException $exception) {
            $this->errors = $exception->errors();

            return;
        }

        $user = User::create($validated);

        $this->success = true;
        $this->userId = $user->getAttribute('id');
        $this->name = $user->getAttribute('name');
        $this->email = $user->getAttribute('email');
        $this->redirect('/');
    }
}
