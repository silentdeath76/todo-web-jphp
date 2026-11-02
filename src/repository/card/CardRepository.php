<?php

namespace repository\card;


use entities\card\CardData;
use entities\card\CardDraftData;

interface CardRepository
{
    /**
     * @return CardData[]
     */
    public function getAllCards(int $userId): array;

    public function getCard(int $id, int $userId): ?CardData;

    public function addCard(int $id, CardDraftData $draft, int $userId): CardData;

    public function updateCard(int $id, CardDraftData $draft): bool;

    public function deleteCard(int $id, int $userId): bool;
}