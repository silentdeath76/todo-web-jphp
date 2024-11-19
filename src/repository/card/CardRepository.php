<?php

namespace repository\card;


use entities\card\CardData;
use entities\card\CardDraftData;

interface CardRepository
{
    /**
     * @return CardData[]
     */
    public function getAllCards(): array;

    public function getCard(int $id): ?CardData;

    public function addCard(CardDraftData $draft): CardData;

    public function updateCard(int $id, CardDraftData $draft): bool;

    public function deleteCard(int $id): bool;
}