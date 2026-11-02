<?php

namespace repository\task;

use entities\task\Task;
use entities\task\TaskDraft;

interface TaskRepository
{
    public function getAllTask(): array;
    public function getTask(int $id): ?Task;
    public function addTask(TaskDraft $draft, int $userId): Task;
    public function removeTask(int $id, int $userId): bool;
    public function updateTask(int $id, TaskDraft $draft, int $userId): bool;
    public function getTaskForCard(int $cardId, int $userId): array;
}