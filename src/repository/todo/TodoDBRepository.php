<?php

namespace repository\todo;

use core\logger\Logger;
use entities\todo\ToDo;
use entities\todo\ToDoDraft;
use php\sql\SqlException;
use repository\SqliteRepository;

class TodoDBRepository extends SqliteRepository implements ToDoRepository
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

    public function getAllToDos(): array
    {
        return flow($this->query("SELECT * FROM {$this->table}"))->map(function ($item) {
            $item = $item->toArray();
            return new ToDo($item["id"], $item["task"], (bool)$item["done"], $item["cardId"]);
        })->toArray();
    }

    public function getToDo(int $id): ?ToDo
    {
        // TODO: Implement getToDo() method.
        Logger::info(__METHOD__);
    }

    public function addToDo(ToDoDraft $draft): ToDo
    {
        $result = $this->query("INSERT INTO {$this->table} (task, done, cardId) values (?,?,?)", [$draft->title, $draft->done, $draft->cardId])->update();
        // todo check $result on > 0 ???


        $item = $this->query("SELECT * FROM {$this->table} where task=? and cardId=?", [$draft->title, $draft->cardId])->fetch();

        if ($item == null) {
            throw new \Exception("Not found last inserted task");
        }

        $item = $item->toArray();
        return new ToDo($item["id"], $item["task"], (bool)$item["done"], $item["cardId"]);
    }

    public function removeTodo(int $id): bool
    {
        // TODO: Implement removeTodo() method.
        Logger::info(__METHOD__);
    }

    public function updateToDo(int $id, ToDoDraft $draft): bool
    {
        try {
            return (bool)$this->query("UPDATE {$this->table} SET task=?, done=? WHERE id=? and cardId = ?", [$draft->title, $draft->done, $id, $draft->cardId])->update();
        } catch (SqlException $e) {
            Logger::error("cannot update task: " . $e->getMessage());
            return false;
        }
    }

    public function getToDoForCard(int $cardId): array
    {
        return flow($this->query("SELECT * FROM {$this->table} WHERE cardId=?", [$cardId]))->map(function ($item) {
            $item = $item->toArray();
            return new ToDo($item["id"], $item["task"], (bool)$item["done"], $item["cardId"]);
        })->toArray();
    }
}