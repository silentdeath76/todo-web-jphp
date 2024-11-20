<?php

namespace entities\task;

class TaskDraft
{
    public $title;
    public $done;
    public $cardId;


    public function __construct(string $title, bool $done, $cardId)
    {
        $this->title = $title;
        $this->done = $done;
        $this->cardId = $cardId;

    }
}