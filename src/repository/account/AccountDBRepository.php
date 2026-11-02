<?php

namespace repository\account;

use core\logger\Logger;
use entities\account\AccountData;
use entities\account\AccountDraftData;
use php\sql\SqlException;
use repository\SqliteRepository;

class AccountDBRepository extends SqliteRepository implements AccountRepository
{
    private $table = "users";

    public function makeTable()
    {
        try {
            $this->createTable($this->table, [
                "id"         => "integer primary key autoincrement",
                "username"   => "text unique",
                "password"   => "text",
                "email"      => "text",
                "role"       => "integer default 0",
                "created_at" => "integer",
                "updated_at" => "integer",
                "deleted_at" => "integer",
                "deleted"    => "integer default 0",
            ]);

            if ($this->get("admin") == null) {
                $this->create(new AccountDraftData(
                    "admin",
                    \core\auth\PasswordHasher::hash("admin"),
                    "admin@gmail.com",
                    1
                ));
            }
        } catch (SqlException $e) {
            Logger::error("Error create table {$this->table}: " . $e->getMessage());
        }
    }

    public function get($username): ?AccountData
    {
        try {
            $row = $this->query(
                "SELECT * FROM {$this->table} WHERE username = ? AND deleted = 0",
                [$username]
            )->fetch();

            if ($row === null) {
                return null;
            }

            $u = $row->toArray();
            return new AccountData(
                (int)$u["id"],
                $u["username"],
                $u["password"],
                $u["email"],
                (int)$u["role"],
                (int)$u["created_at"],
                (int)$u["updated_at"]
            );
        } catch (SqlException $e) {
            Logger::error("Error get user {$username}: " . $e->getMessage());
            return null;
        }
    }

    public function getById(int $id): ?AccountData
    {
        try {
            $row = $this->query(
                "SELECT * FROM {$this->table} WHERE id = ? AND deleted = 0",
                [$id]
            )->fetch();
            if ($row === null) return null;
            $u = $row->toArray();
            return new AccountData(
                (int)$u["id"], $u["username"], $u["password"], $u["email"],
                (int)$u["role"], (int)$u["created_at"], (int)$u["updated_at"]
            );
        } catch (SqlException $e) {
            Logger::error("getById: " . $e->getMessage());
            return null;
        }
    }

    public function create(AccountDraftData $account): ?AccountData
    {
        try {
            $now = time();
            $this->query(
                "INSERT INTO {$this->table} (username, password, email, role, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?)",
                [$account->username, $account->password, $account->email, $account->role, $now, $now]
            )->update();

            return $this->get($account->username);
        } catch (SqlException $e) {
            Logger::error("Error create account: " . $e->getMessage());
            return null;
        }
    }

    public function update(int $id, AccountDraftData $account): bool
    {
        try {
            $this->query("UPDATE $this->table SET username = ?, password = ?, email = ?, role = ? WHERE id = ?",
                [$account->username, $account->password, $account->email, $account->role, $id])->update();
            return true;
        } catch (SqlException $e) {
            Logger::error("Error update account");
            return false;
        }
    }

    public function getAll(): array
    {
        try {
            $accounts = [];
            foreach ($this->query("SELECT * FROM {$this->table} WHERE deleted = 0") as $row) {
                $u = $row->toArray();
                $accounts[] = new AccountData(
                    (int)$u["id"], $u["username"], $u["password"], $u["email"],
                    (int)$u["role"], (int)$u["created_at"], (int)$u["updated_at"]
                );
            }
            return $accounts;
        } catch (SqlException $e) {
            Logger::error("Error get all accounts: " . $e->getMessage());
            return [];
        }
    }
}