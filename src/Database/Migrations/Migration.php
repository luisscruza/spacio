<?php

namespace Spacio\Framework\Database\Migrations;

use Spacio\Framework\Database\Contracts\ConnectionInterface;

abstract class Migration
{
    abstract public function up(ConnectionInterface $connection): void;
}
