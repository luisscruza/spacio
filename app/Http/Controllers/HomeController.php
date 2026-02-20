<?php

namespace App\Http\Controllers;

use Spacio\Framework\Http\Response;

class HomeController
{
    public function index(): Response
    {
        return view('home');
    }
}
