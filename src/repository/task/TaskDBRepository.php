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
        // TODO: Implement getToDo() method.
        Logger::info(__METHOD__);
    }

    public function addTask(TaskDraft $draft): Task
    {
        $result = $this->query("INSERT INTO {$this->table} (task, done, cardId) values (?,?,?)", [$draft->title, $draft->done, $draft->cardId])->update();
        // todo check $result on > 0 ???


        $item = $this->query("SELECT * FROM {$this->table} where task=? and cardId=?", [$draft->title, $draft->cardId])->fetch();

        if ($item == null) {
            throw new \Exception("Not found last inserted task");
        }

        $item = $item->toArray();
        return new Task($item["id"], $item["task"], (bool)$item["done"], $item["cardId"]);
    }

    public function removeTask(int $id): bool
    {
        // TODO: Implement removeTodo() method.
        Logger::info(__METHOD__);
    }

    public function updateTask(int $id, TaskDraft $draft): bool
    {
        try {
            return (bool)$this->query("UPDATE {$this->table} SET task=?, done=? WHERE id=? and cardId = ?", [$draft->title, $draft->done, $id, $draft->cardId])->update();
        } catch (SqlException $e) {
            Logger::error("cannot update task: " . $e->getMessage());
            return false;
        }
    }

    public function getTaskForCard(int $cardId): array
    {
        return flow($this->query("SELECT * FROM {$this->table} WHERE cardId=?", [$cardId]))->map(function ($item) {
            $item = $item->toArray();
            return new Task($item["id"], $item["task"], (bool)$item["done"], $item["cardId"]);
        })->toArray();
    }
}