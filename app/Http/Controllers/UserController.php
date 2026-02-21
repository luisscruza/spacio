<?php

namespace App\Http\Controllers;

use App\Entities\User;
use Spacio\Framework\Http\Response;

class UserController
{
    public function show(User $user): Response
    {
        return view('users.show', [
            'user' => $user,
        ]);
    }
}
