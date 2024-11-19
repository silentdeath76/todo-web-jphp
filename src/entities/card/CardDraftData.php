<?php

namespace entities\card;

class CardDraftData
{
    public $title;
    public $details;

    /**
     * @param $title
     * @param $details
     */
    public function __construct($title, $details)
    {
        $this->title = $title;
        $this->details = $details;
    }

}