<?php

namespace repository\task;

use core\logger\Logger;
use entities\task\Task;
use entities\task\TaskDraft;
use php\sql\SqlException;
use repository\SqliteRepository;

class TaskDBRepository extends SqliteRepository implements TaskRepository
{

    private $table = "task";

    public function makeTable()
    {
        try {
            $this->createTable($this->table, [
                "id" => "integer primary key autoincrement",
                "task" => "text",
                "done" => "boolean",
                "cardId" => "integer",
            ]);

        } catch (SqlException $e) {
            Logger::error("cannot creating table: " . $e->getMessage());
        }
    }

    public function getAllTask(): array
    {
        return flow($this->query("SELECT * FROM {$this->table}"))->map(function ($item) {
            $item = $item->toArray();
            return new Task($item["id"], $item["task"], (bool)$item["done"], $item["cardId"]);
        })->toArray();
    }

    public function getTask(int $id): ?Task
    {
        $row = $this->query(
            "SELECT t.* FROM {$this->table} t
         INNER JOIN cards c ON c.id = t.cardId
         WHERE t.id = ? AND c.userId = ?",
            [$id, $userId]
        )->fetch();
        if ($row === null) return null;
        $i = $row->toArray();
        return new Task((int)$i["id"], $i["task"], (bool)$i["done"], (int)$i["cardId"]);
    }

    public function addTask(TaskDraft $draft, int $userId): Task
    {
        $check = $this->query(
            "SELECT 1 FROM cards WHERE id=? AND userId=?",
            [$draft->cardId, $userId]
        )->fetch();
        if ($check === null) {
            throw new \Exception("Card not found or access denied");
        }

        $this->query(
            "INSERT INTO {$this->table} (task, done, cardId) VALUES (?,?,?)",
            [$draft->title, $draft->done ? 1 : 0, $draft->cardId]
        )->update();

        $row = $this->query(
            "SELECT * FROM {$this->table} WHERE id = last_insert_rowid()"
        )->fetch();

        if ($row === null) {
            throw new \Exception("Cannot find inserted task");
        }

        $i = $row->toArray();
        return new Task((int)$i["id"], $i["task"], (bool)$i["done"], (int)$i["cardId"]);
    }

    public function removeTask(int $id, int $userId): bool
    {
        $this->query(
            "DELETE FROM {$this->table}
         WHERE id = ? AND cardId IN (SELECT id FROM cards WHERE userId = ?)",
            [$id, $userId]
        )->update();
        return true;
    }

    public function updateTask(int $id, TaskDraft $draft, int $userId): bool
    {
        try {
            return (bool)$this->query("UPDATE {$this->table} SET task=?, done=? WHERE id=? and cardId = ?", [$draft->title, $draft->done, $id, $draft->cardId])->update();
        } catch (SqlException $e) {
            Logger::error("cannot update task: " . $e->getMessage());
            return false;
        }
    }

    public function getTaskForCard(int $cardId, int $userId): array
    {
        // проверка через join с cards
        return flow(
            $this->query(
                "SELECT t.* FROM {$this->table} t
             INNER JOIN cards c ON c.id = t.cardId
             WHERE t.cardId = ? AND c.userId = ?",
                [$cardId, $userId]
            )
        )->map(function ($item) {
            $i = $item->toArray();
            return new Task($i["id"], $i["task"], (bool)$i["done"], $i["cardId"]);
        })->toArray();
    }
}