<?php

namespace core\auth;

class PasswordHasher
{
    const SALT = "TaskApp_2026_secret_salt_change_me";

    public static function hash(string $password): string
    {
        return md5(self::SALT . $password . self::SALT);
    }

    public static function verify(string $password, string $hash): bool
    {
        return self::hash($password) === $hash;
    }
}