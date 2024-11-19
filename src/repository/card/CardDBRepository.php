<?php

namespace repository\card;

use core\logger\Logger;
use entities\card\CardData;
use entities\card\CardDraftData;
use php\sql\SqlException;
use repository\SqliteRepository;

class CardDBRepository extends SqliteRepository
{
    /**
     * @var string
     */
    private $table = "cards";

    public function makeTable()
    {
        try {
            $this->createTable($this->table, [
                "id" => "integer primary key autoincrement",
                "title" => "text",
                "details" => "text",
                "userId" => "integer"
            ]);


            return;
            $this->addCard(new CardDraftData("First card", "short description a card", 1));
            $this->addCard(new CardDraftData("Second card", "short description a card", 1));
        } catch (SqlException $e) {
            Logger::error("cannot creating table: " . $e->getMessage());
        }
    }

    public function getAllCards(): array
    {
        try {
            $cardList = [];

            foreach ($this->query("SELECT * FROM $this->table") as $item) {
                $item = $item->toArray();
                $cardList[] = new CardData($item["id"], $item["title"], $item["details"]);
            }

            return $cardList;
        } catch (SqlException $e) {
            Logger::error("getting all cards: " . $e->getMessage());
            return [];
        }
    }

    public function getCard(int $id): ?CardData
    {
        try {
            $card = $this->query("SELECT * FROM $this->table WHERE id = ?", [$id])->fetch()->toArray();
            return new CardData($card["id"], $card["title"], $card["details"]);
        } catch (SqlException $e) {
            Logger::error("getting card: " . $e->getMessage());
            return null;
        }

    }

    public function addCard(CardDraftData $draft): ?CardData
    {
        try {
            $this->query("INSERT INTO {$this->table} (title, details) VALUES (?,?)", [$draft->title, $draft->details])->update();
            $card = $this->query("SELECT * FROM {$this->table} where title = ? and details = ?", [$draft->title, $draft->details])->fetch()->toArray();

            return new CardData($card["id"], $card["title"], $card["details"]);
        } catch (SqlException $e) {
            Logger::error("adding card: " . $e->getMessage());
        }

        return null;
    }

    public function updateCard(int $id, CardDraftData $draft): bool
    {
        try {
            $this->query("UPDATE $this->table SET title =?, details =? WHERE id =?", [$draft->title, $draft->details, $id])->update();
            return true;
        } catch (SqlException $e) {
            Logger::error("updating card: " . $e->getMessage());
        }

        return false;
    }

    public function deleteCard(int $id): bool
    {
        try {
            $this->query("DELETE FROM $this->table WHERE id =?", [$id])->update();
            return true;
        } catch (SqlException $e) {
            Logger::error("deleting card: " . $e->getMessage());
        }

        return false;
    }
}