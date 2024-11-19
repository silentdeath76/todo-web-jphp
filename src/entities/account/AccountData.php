<?php

namespace entities\account;

class AccountData
{
    public $id;
    public $username;
    public $password;
    public $email;
    public $role;
    public $created_at;
    public $updated_at;

    /**
     * @param $id
     * @param $username
     * @param $password
     * @param $email
     * @param int $role
     * @param null $created_at
     * @param null $updated_at
     */
    public function __construct($id, $username, $password, $email, int $role = 0, $created_at = null, $updated_at = null)
    {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->email = $email;
        $this->role = $role;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }


}