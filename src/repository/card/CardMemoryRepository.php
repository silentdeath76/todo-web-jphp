<?php

namespace repository\card;

use entities\card\CardData;
use entities\card\CardDraftData;
use repository\AbstractRepository;

class CardMemoryRepository extends AbstractRepository implements CardRepository
{

    public function __construct()
    {
        // test data
        $this->addCard(
            new CardDraftData(
                "Ваша первая карточка",
                "Тут хранятся ваши сгруппированные заметки первой карточки"
            )
        );

        $this->addCard(
            new CardDraftData(
                "Ваша Вторая карточка",
                "Тут хранятся ваши сгруппированные заметки второй карточки"
            )
        );
    }

    public function getAllCards(): array
    {
        return $this->itemsList;
    }

    public function getCard(int $id): ?CardData
    {
        return flow($this->itemsList)->findOne(function (CardData $cardData) use ($id) {
            return $cardData->id === $id;
        });
    }

    public function addCard(CardDraftData $draft): CardData
    {
        $index = 1;

        if (count($this->itemsList) > 0) {
            $index = $this->itemsList[count($this->itemsList) - 1]->id + 1;
        }

        $this->itemsList[] = $item = new CardData($index, $draft->title, $draft->details);

        return $item;
    }

    public function updateCard(int $id, CardDraftData $draft): bool
    {
        $item = $this->getCard($id);

        if ($item == null) {
            return false;
        }

        $item->update($draft);

        return true;
    }

    public function deleteCard(int $id): bool
    {
        foreach ($this->itemsList as $key => $card) {
            if ($card->id === $id) {
                unset($this->itemsList[$key]);
                $this->updateListKeys();
                return true;
            }
        }

        return false;
    }
}