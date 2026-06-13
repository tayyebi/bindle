<?php

namespace App\Core;

class Database
{
    private ?\PDO $pdo = null;
    private array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    private function connect(): void
    {
        if ($this->pdo !== null) return;

        $dsn = sprintf(
            'pgsql:host=%s;port=%s;dbname=%s',
            $this->config['host'],
            $this->config['port'],
            $this->config['name']
        );

        $this->pdo = new \PDO($dsn, $this->config['user'], $this->config['password'], [
            \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
            \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
            \PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    }

    public function pdo(): \PDO
    {
        $this->connect();
        return $this->pdo;
    }

    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetch(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    public function insert(string $table, array $data): string
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = ':' . implode(', :', array_keys($data));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders}) RETURNING id";
        return $this->fetch($sql, $data)['id'];
    }

    public function update(string $table, array $data, string $where, array $whereParams = []): void
    {
        $sets = [];
        $params = [];
        foreach ($data as $key => $value) {
            $sets[] = "{$key} = :set_{$key}";
            $params["set_{$key}"] = $value;
        }
        foreach ($whereParams as $key => $value) {
            $params[$key] = $value;
        }
        $sql = "UPDATE {$table} SET " . implode(', ', $sets) . " WHERE {$where}";
        $this->query($sql, $params);
    }

    public function beginTransaction(): void
    {
        $this->pdo()->beginTransaction();
    }

    public function commit(): void
    {
        $this->pdo()->commit();
    }

    public function rollback(): void
    {
        $this->pdo()->rollBack();
    }
}
