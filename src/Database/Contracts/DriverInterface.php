<?php

namespace Spacio\Framework\Database\Contracts;

interface DriverInterface
{
    public function connect(array $config): ConnectionInterface;
}
