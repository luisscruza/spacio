<?php

namespace App\Http\Controllers;

use App\Entities\User;
use Spacio\Framework\Http\Response;

class HomeController
{
    public function index(): Response
    {
        $users = (new User)->all();

        dd($users);
        return view('home');
    }
}
