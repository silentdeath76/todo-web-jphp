<?php

namespace repository\account;

use entities\account\AccountData;
use entities\account\AccountDraftData;

interface AccountRepository
{
    public function get($username): ?AccountData;

    public function create(AccountDraftData $account): ?AccountData;

    public function update(int $id, AccountDraftData $account): bool;

    public function getById(int $id): ?AccountData;

    /**
     * @return AccountData[]
     */
    public function getAll(): array;
}