<?php

namespace repository\task;

use entities\task\Task;
use entities\task\TaskDraft;

interface TaskRepository
{
    /**
     * @return Task[]
     */
    public function getAllTask(): array;

    public function getTask(int $id): ?Task;

    public function addTask(TaskDraft $draft): Task;

    public function removeTask(int $id): bool;

    public function updateTask(int $id, TaskDraft $draft): bool;

    public function getTaskForCard(int $cardId): array;
}