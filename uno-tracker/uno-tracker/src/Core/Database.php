<?php

namespace Core;

use PDO;
use PDOException;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;
    private array $config;

    private function __construct()
    {
        $this->config = require dirname(__DIR__, 2) . '/config/database.php';
        $this->connect();
    }

    private function connect(): void
    {
        $dsn = sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            $this->config['driver'],
            $this->config['host'],
            $this->config['port'],
            $this->config['database'],
            $this->config['charset']
        );

        try {
            $this->pdo = new PDO(
                $dsn,
                $this->config['username'],
                $this->config['password'],
                $this->config['options']
            );
        } catch (PDOException $e) {
            throw new PDOException('Database connection failed: ' . $e->getMessage());
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * اجرای کوئری با پارامترها
     */
    public function query(string $sql, array $params = []): PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * گرفتن یک ردیف
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $stmt = $this->query($sql, $params);
        $result = $stmt->fetch();
        return $result ?: null;
    }

    /**
     * گرفتن چندین ردیف
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    /**
     * گرفتن یک مقدار
     */
    public function fetchValue(string $sql, array $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn();
    }

    /**
     * Insert و برگرداندن ID
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $sql = "INSERT INTO `{$table}` ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));

        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update
     */
    public function update(string $table, array $data, string $where, array $whereParams = []): int
    {
        $set = [];
        $params = [];

        foreach ($data as $column => $value) {
            $set[] = "`{$column}` = ?";
            $params[] = $value;
        }

        $sql = "UPDATE `{$table}` SET " . implode(', ', $set) . " WHERE {$where}";
        $params = array_merge($params, $whereParams);

        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * Delete
     */
    public function delete(string $table, string $where, array $params = []): int
    {
        $sql = "DELETE FROM `{$table}` WHERE {$where}";
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    /**
     * شروع Transaction
     */
    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    /**
     * Commit Transaction
     */
    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    /**
     * Rollback Transaction
     */
    public function rollback(): bool
    {
        return $this->pdo->rollBack();
    }

    /**
     * اجرای یک عملیات در Transaction
     */
    public function transaction(callable $callback): mixed
    {
        $this->beginTransaction();

        try {
            $result = $callback($this);
            $this->commit();
            return $result;
        } catch (\Throwable $e) {
            $this->rollback();
            throw $e;
        }
    }

    /**
     * Row Locking برای جلوگیری از Race Condition
     */
    public function lockForUpdate(string $table, string $where, array $params = []): ?array
    {
        $sql = "SELECT * FROM `{$table}` WHERE {$where} FOR UPDATE";
        return $this->fetchOne($sql, $params);
    }

    // جلوگیری از Clone و Unserialize
    private function __clone() {}
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize singleton");
    }

    /**
     * گرفتن تعداد ردیف‌های تحت تأثیر
     */
    public function getAffectedRows(): int
    {
        return $this->pdo->rowCount();
    }
}
