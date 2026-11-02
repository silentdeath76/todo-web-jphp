<?php

namespace core\auth;

use core\logger\Logger;
use php\http\HttpServerRequest;
use entities\account\AccountData;
use repository\account\AccountRepository;
use repository\session\SessionRepository;

class Auth
{
    const COOKIE = "session_token";

    /** @var AccountRepository */
    private $accounts;
    /** @var SessionRepository */
    private $sessions;

    public function __construct(AccountRepository $accounts, SessionRepository $sessions)
    {
        $this->accounts = $accounts;
        $this->sessions = $sessions;
    }

    public function login(string $username, string $password): ?array
    {
        $user = $this->accounts->get($username);
        if ($user === null) return null;

        if (!\core\auth\PasswordHasher::verify($password, $user->password)) {
            return null;
        }

        $token = md5(uniqid((string)mt_rand(), true)) . md5(time() . $username);
        $this->sessions->create($user->id, $token);

        return ["token" => $token, "user" => $user];
    }

    public function logout(string $token): void
    {
        $this->sessions->delete($token);
    }

    public function user(HttpServerRequest $request): ?AccountData
    {
        try {
            $cookies = $request->cookies();
            //Logger::info("cookies RAW = " . json_encode($cookies));

            $cookie = $cookies[self::COOKIE] ?? null;
            //Logger::info("cookie entry = " . json_encode($cookie));

            if ($cookie === null) {
                Logger::info("no cookie entry");
                return null;
            }

            // Достаём value из объекта Cookie
            $token = null;
            if (is_object($cookie)) {
                $token = $cookie->value ?? null;
                //Logger::info("cookie is object, value = " . var_export($token, true));
            } else if (is_array($cookie)) {
                $token = $cookie["value"] ?? null;
                //Logger::info("cookie is array, value = " . var_export($token, true));
            } else if (is_string($cookie)) {
                $token = $cookie;
                //Logger::info("cookie is string = " . $token);
            }

            if (!$token || !is_string($token)) {
                //Logger::info("token invalid: " . gettype($token));
                return null;
            }

            //Logger::info("calling findUserId with token = $token");
            $userId = $this->sessions->findUserId($token);
            //Logger::info("findUserId returned = " . var_export($userId, true));

            if ($userId === null) {
                return null;
            }

            $user = $this->accounts->getById($userId);
            //Logger::info("getById returned = " . ($user ? $user->username : "null"));
            return $user;
        } catch (\Throwable $e) {
            //Logger::error("Auth::user error: " . $e->getMessage());
            return null;
        }
    }

    public function requireUser(HttpServerRequest $request): ?AccountData
    {
        return $this->user($request);
    }
}