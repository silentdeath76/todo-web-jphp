<?php

namespace repository\task;

use entities\task\Task;
use entities\task\TaskDraft;
use repository\AbstractRepository;

class TaskMemoryRepository extends AbstractRepository implements TaskRepository
{
    public function __construct()
    {
        // test data
        /* $this->addToDo(new ToDoDraft('Go to the gym', false, 1));
        $this->addToDo(new ToDoDraft('Go to the gym 1', true, 2));
        $this->addToDo(new ToDoDraft('Go to the gym 2', false, 2)); */
    }


    /**
     * @return array|Task[]
     */
    public function getAllTask(): array
    {
        return $this->itemsList;
    }

    /**
     * @param int $id
     * @return Task
     */
    public function getTask(int $id): ?Task
    {
        return flow($this->itemsList)->findOne(function ($item) use ($id) {
            return $item->getId() === $id;
        });
    }

    public function addTask(TaskDraft $draft): Task
    {
        $index = 0;

        if (count($this->itemsList) > 0) {
            $index = $this->itemsList[count($this->itemsList) - 1]->getId() + 1;
        }

        $this->itemsList[] = $item = new Task($index, $draft->title, $draft->done, $draft->cardId);

        return $item;
    }

    public function removeTask(int $id): bool
    {
        foreach ($this->itemsList as $key => $toDo) {
            if ($toDo->getId() === $id) {
                unset($this->itemsList[$key]);
                $this->updateListKeys();
                return true;
            }
        }

        return false;
    }

    public function updateTask(int $id, TaskDraft $draft): bool
    {
        $item = $this->getTask($id);

        if ($item == null) {
            return false;
        }

        $item->update($draft);

        return true;
    }

    public function getTaskForCard(int $cardId): array
    {
        return array_filter($this->itemsList,
            fn($item) => $item->getCardId() === $cardId);
    }

}