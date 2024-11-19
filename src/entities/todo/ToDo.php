<?php

namespace entities\todo;

class ToDo
{
    /**
     * @var mixed
     */
    private $id;
    /**
     * @var mixed
     */
    private $title;
    /**
     * @var bool
     */
    private $done;

    /**
     * @var int
     */
    private $cardId;

    public function __construct($id, $title, bool $done, $cardId)
    {
        $this->id = $id;
        $this->title = $title;
        $this->done = $done;
        $this->cardId = $cardId;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function isDone(): bool
    {
        return $this->done;
    }

    public function getCardId(): int
    {
        return $this->cardId;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "done" => $this->done,
            "cardId" => $this->cardId
        ];
    }

    public function update(ToDoDraft $draft)
    {
        $this->title = $draft->title;
        $this->done = $draft->done;
    }

}