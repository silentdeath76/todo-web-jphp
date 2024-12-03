<?php

namespace entities\account;

use routes\account\CreateAccount;

class AccountDraftData
{
    public $username;
    public $password;
    public $email;
    public $role;
    public $updated_ad;

    /**
     * @param $username
     * @param $password
     * @param $email
     * @param int $role
     */
    public function __construct($username, $password, $email, int $role = CreateAccount::USER_ROLE_DEFAULT)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
        $this->updated_ad = time();
    }


}