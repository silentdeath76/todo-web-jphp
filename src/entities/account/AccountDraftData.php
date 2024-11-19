<?php

namespace entities\account;

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
     * @param $role
     */
    public function __construct($username, $password, $email, $role)
    {
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
        $this->updated_ad = time();
    }


}