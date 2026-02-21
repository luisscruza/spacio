<?php

use Spacio\Framework\Database\Migrations\Migration;
use Spacio\Framework\Database\Contracts\ConnectionInterface;
use Spacio\Framework\Database\Schema\Table;

return new class extends Migration {
    public function up(ConnectionInterface $connection): void
    {
        Table::create('users', [
            'id' => ['integer', 'pk', 'autoincrement'],
            'name' => ['string'],
            'email' => ['string', 'unique']
        ], $connection);
    }
};
