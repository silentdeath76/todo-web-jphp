<?php

namespace repository\todo;

use entities\todo\ToDo;
use entities\todo\ToDoDraft;

interface ToDoRepository
{
    /**
     * @return ToDo[]
     */
    public function getAllToDos(): array;

    public function getToDo(int $id): ?ToDo;

    public function addToDo(ToDoDraft $draft): ToDo;

    public function removeTodo(int $id): bool;

    public function updateToDo(int $id, ToDoDraft $draft): bool;

    public function getToDoForCard(int $cardId): array;
}