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
                "id"      => "integer primary key autoincrement",
                "title"   => "text",
                "details" => "text",
                "userId"  => "integer not null default 0",
            ]);


            return;
            $this->addCard(new CardDraftData("First card", "short description a card", 1));
            $this->addCard(new CardDraftData("Second card", "short description a card", 1));
        } catch (SqlException $e) {
            Logger::error("cannot creating table: " . $e->getMessage());
        }
    }

    public function getAllCards(int $userId): array
    {
        try {
            $list = [];
            foreach ($this->query("SELECT * FROM {$this->table} WHERE userId = ?", [$userId]) as $item) {
                $i = $item->toArray();
                $list[] = new CardData($i["id"], $i["title"], $i["details"]);
            }
            return $list;
        } catch (SqlException $e) {
            \core\logger\Logger::error("getting all cards: " . $e->getMessage());
            return [];
        }
    }

    public function getCard(int $id, int $userId): ?CardData
    {
        try {
            $row = $this->query(
                "SELECT * FROM {$this->table} WHERE id = ? AND userId = ?",
                [$id, $userId]
            )->fetch();
            if ($row === null) return null;
            $c = $row->toArray();
            return new CardData($c["id"], $c["title"], $c["details"]);
        } catch (SqlException $e) {
            return null;
        }
    }

    public function addCard(CardDraftData $draft, int $userId): ?CardData
    {
        try {
            $this->query(
                "INSERT INTO {$this->table} (title, details, userId) VALUES (?,?,?)",
                [$draft->title, $draft->details, $userId]
            )->update();
            $row = $this->query(
                "SELECT * FROM {$this->table} WHERE id = last_insert_rowid()"
            )->fetch();
            if ($row === null) return null;
            $c = $row->toArray();
            return new CardData($c["id"], $c["title"], $c["details"]);
        } catch (SqlException $e) {
            return null;
        }
    }

    public function updateCard(int $id, CardDraftData $draft, int $userId): bool
    {
        try {
            $this->query(
                "UPDATE {$this->table} SET title=?, details=? WHERE id=? AND userId=?",
                [$draft->title, $draft->details, $id, $userId]
            )->update();
            return true;
        } catch (SqlException $e) {
            return false;
        }
    }

    public function deleteCard(int $id, int $userId): bool
    {
        try {
            $this->query(
                "DELETE FROM {$this->table} WHERE id=? AND userId=?",
                [$id, $userId]
            )->update();
            return true;
        } catch (SqlException $e) {
            return false;
        }
    }
}