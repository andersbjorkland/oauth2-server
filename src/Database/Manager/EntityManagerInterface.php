<?php

namespace App\Database\Manager;

interface EntityManagerInterface
{
    public function create(mixed $entity): bool;
    public function update(mixed $entity): bool;
    public function delete(mixed $entity): bool;
    public function get(string $id): mixed;
}