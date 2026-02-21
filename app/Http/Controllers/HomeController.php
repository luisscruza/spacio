<?php

namespace App\Http\Controllers;

use App\Entities\User;
use Spacio\Framework\Http\Attributes\Route;
use Spacio\Framework\Http\Response;

class HomeController
{
    #[Route('/', name: 'home')]
    public function index(): Response
    {
        $users = User::query()->get();

        return view('home', ['users' => $users]);
    }
}
