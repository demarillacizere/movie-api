<?php

namespace MovieApi\Models;

use MovieApi\App\DB;
use DI\Container;
use PDO;

abstract class A_Model
{
    private ?PDO $pdo;

    abstract function findAll(): array;

    abstract function findById(int $id): array;

    abstract function update(array $data): bool;

    abstract function insert(array $data): int;

    abstract function delete(int $id): bool;

    public function __construct(Container $container)
    {
        $this->pdo = $container->get('database');
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }
}