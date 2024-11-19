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
                    "id" => "integer primary key autoincrement",
                    "username" => "text unique",
                    "password" => "text",
                    "email" => "text",
                    "role" => "integer default 0",
                    "created_at" => "datetime default current_timestamp",
                    "updated_at" => "datetime default current_timestamp",
                    "deleted_at" => "datetime default current_timestamp",
                    "deleted" => "boolean default 0",
                ]
            );

            if ($this->get("admin") == null) {
                // todo change password hashing with use a salt
                $this->create(new AccountDraftData("admin", md5("admin"), "admin@gmail.com", 1));
            }
        } catch (SqlException $e) {
            Logger::error("Error create table $this->table");
        }
    }

    public function get($username): ?AccountData
    {
        try {
            if (($fetch = $this->query("SELECT * FROM $this->table WHERE username = ?", [$username])->fetch()) !== null) {
                $user = $fetch->toArray();

                if ($user["deleted"] === 0) {
                    return new AccountData($user["id"], $user["username"], $user["password"], $user["email"], $user["role"], $user["created_at"], $user["updated_at"]);
                }
            }

        } catch (SqlException $e) {
            Logger::error("Error get user $username");
        }

        return null;
    }

    public function create(AccountDraftData $account): ?AccountData
    {
        try {
            $this->query("INSERT INTO $this->table (username, password, email) VALUES (?, ?, ?)",
                [$account->username, $account->password, $account->email])->update();

            return $this->get($account->username);
        } catch (SqlException $e) {
            Logger::error("Error create account");
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
            foreach ($this->query("SELECT * FROM $this->table WHERE deleted = 0") as $user) {
                $user = $user->toArray();
                $accounts[] = new AccountData($user["id"], $user["username"], $user["password"], $user["email"], $user["role"], $user["created_at"], $user["updated_at"]);
            }
            return $accounts;
        } catch (SqlException $e) {
            Logger::error("Error get all accounts");
            return [];
        }
    }
}