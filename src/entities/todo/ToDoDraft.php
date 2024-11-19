<?php

namespace entities\todo;

class ToDoDraft
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