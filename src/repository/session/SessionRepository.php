<?php

namespace repository\session;

use core\logger\Logger;
use php\sql\SqlException;
use repository\SqliteRepository;

class SessionRepository extends SqliteRepository
{
    private $table = "sessions";

    public function makeTable()
    {
        try {
            $this->createTable($this->table, [
                "token"      => "text primary key",
                "userId"     => "integer not null",
                "created_at" => "integer not null",
                "expires_at" => "integer not null",
            ]);
        } catch (SqlException $e) {
            Logger::error("Error create table {$this->table}: " . $e->getMessage());
        }
    }

    public function create(int $userId, string $token, int $ttlSeconds = 86400): bool
    {
        try {
            $now = time();
            $this->query(
                "INSERT INTO {$this->table} (token, userId, created_at, expires_at) VALUES (?, ?, ?, ?)",
                [$token, $userId, $now, $now + $ttlSeconds]
            )->update();
            return true;
        } catch (SqlException $e) {
            Logger::error("Session create: " . $e->getMessage());
            return false;
        }
    }

    public function findUserId(string $token): ?int
    {
        try {
            $row = $this->query(
                "SELECT userId FROM {$this->table} WHERE token = ? AND expires_at > ?",
                [$token, time()]
            )->fetch();
            // Logger::info(__METHOD__);
            // var_dump($row->toArray());
            return $row ? (int)$row->toArray()["userId"] : null;
        } catch (SqlException $e) {
            Logger::error("Session find: " . $e->getMessage());
            return null;
        }
    }

    public function delete(string $token): bool
    {
        try {
            $this->query("DELETE FROM {$this->table} WHERE token = ?", [$token])->update();
            return true;
        } catch (SqlException $e) {
            return false;
        }
    }
}