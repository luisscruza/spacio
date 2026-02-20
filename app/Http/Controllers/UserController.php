<?php

namespace App\Http\Controllers;

use Spacio\Framework\Http\Response;

class UserController
{
    public function show($id): Response
    {
        return new Response('Welcome to'." User ID: $id");
    }
}
