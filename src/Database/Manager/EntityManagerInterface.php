<?php

namespace App\Database\Manager;

use React\MySQL\ConnectionInterface;

interface EntityManagerInterface
{
    public function create(mixed $entity): bool;
    public function update(mixed $entity): bool;
    public function delete(mixed $entity): bool;
    public function get(string $id): mixed;
    public function getConnection(): ConnectionInterface;
}