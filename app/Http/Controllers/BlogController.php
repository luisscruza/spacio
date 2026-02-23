<?php

namespace App\Http\Controllers;

use Spacio\Framework\Http\Attributes\Route;
use Spacio\Framework\Http\Response;

class BlogController
{
    #[Route('/blog', name: 'blog.index')]
    public function index(): Response
    {
        return view('blog.index');
    }
}
