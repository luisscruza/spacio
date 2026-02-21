<?php

namespace App\Http\Controllers;

use App\Entities\User;
use Spacio\Framework\Http\Attributes\Route;
use Spacio\Framework\Http\Request;
use Spacio\Framework\Http\Response;

class UserController
{
    #[Route('/users/{user}', name: 'users.show')]
    public function show(User $user): Response
    {
        return view('users.show', [
            'user' => $user,
        ]);
    }

    #[Route('/users/create', name: 'users.create')]
    public function create(): Response
    {
        return view('users.create');
    }

    #[Route('/users', methods: ['POST'], name: 'users.store')]
    public function store(Request $request): Response
    {
        $user = User::query()->create($request->all());

        return view('users.show', [
            'user' => $user,
        ]);
    }
}
