<?php

namespace repository\todo;

use entities\todo\ToDo;
use entities\todo\ToDoDraft;
use repository\AbstractRepository;

class ToDoMemoryRepository extends AbstractRepository implements ToDoRepository
{
    public function __construct()
    {
        // test data
        /* $this->addToDo(new ToDoDraft('Go to the gym', false, 1));
        $this->addToDo(new ToDoDraft('Go to the gym 1', true, 2));
        $this->addToDo(new ToDoDraft('Go to the gym 2', false, 2)); */
    }


    /**
     * @return array|ToDo[]
     */
    public function getAllToDos(): array
    {
        return $this->itemsList;
    }

    /**
     * @param int $id
     * @return ToDo
     */
    public function getToDo(int $id): ?ToDo
    {
        return flow($this->itemsList)->findOne(function ($item) use ($id) {
            return $item->getId() === $id;
        });
    }

    public function addToDo(ToDoDraft $draft): ToDo
    {
        $index = 0;

        if (count($this->itemsList) > 0) {
            $index = $this->itemsList[count($this->itemsList) - 1]->getId() + 1;
        }

        $this->itemsList[] = $item = new ToDo($index, $draft->title, $draft->done, $draft->cardId);

        return $item;
    }

    public function removeTodo(int $id): bool
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

    public function updateToDo(int $id, ToDoDraft $draft): bool
    {
        $item = $this->getToDo($id);

        if ($item == null) {
            return false;
        }

        $item->update($draft);

        return true;
    }

    public function getToDoForCard(int $cardId): array
    {
        return array_filter($this->itemsList,
            fn($item) => $item->getCardId() === $cardId);
    }

}