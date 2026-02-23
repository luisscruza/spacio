<?php

use Spacio\Framework\Database\Contracts\ConnectionInterface;
use Spacio\Framework\Database\Migrations\Migration;
use Spacio\Framework\Database\Schema\Table;

return new class extends Migration
{
    public function up(ConnectionInterface $connection): void
    {
        Table::create('posts', [
            'id' => ['integer', 'pk', 'autoincrement'],
            'title' => ['string'],
            'slug' => ['string', 'unique'],
            'body' => ['text', 'nullable'],
        ], $connection);
    }
};
