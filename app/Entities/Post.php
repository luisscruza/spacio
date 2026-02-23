<?php

namespace App\Entities;

use Spacio\Framework\Database\Attributes\Table;
use Spacio\Framework\Database\Entity;

#[Table('posts')]
class Post extends Entity
{
    protected ?string $table = 'posts';
}
