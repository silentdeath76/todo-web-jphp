<?php

namespace entities\card;

class CardData
{
    public $id;
    public $title;
    public $details;

    /**
     * @param $id
     * @param $title
     * @param $details
     */
    public function __construct($id, $title, $details)
    {
        $this->id = $id;
        $this->title = $title;
        $this->details = $details;
    }

    public function toArray(): array
    {
        return [
            "id" => $this->id,
            "title" => $this->title,
            "details" => $this->details
        ];
    }

    public function update(CardDraftData $draft)
    {
        $this->title = $draft->title;
        $this->details = $draft->details;
    }

}