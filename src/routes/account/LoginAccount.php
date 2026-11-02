<?php

namespace routes\account;

use core\auth\Auth;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class LoginAccount extends AbstractRoute
{
    /** @var Auth */
    private $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    public function getPath(): string { return "/account/login"; }
    public function getMethod(): string { return "POST"; }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $body = $request->bodyStream()->readFully();
        $params = json_decode($body, true);
        if (!is_array($params)) {
            $params = $request->queryParameters();
        }

        $username = trim($params["username"] ?? "");
        $password = (string)($params["password"] ?? "");

        $res = $this->auth->login($username, $password);

        $response->header("Content-Type", "application/json");

        if ($res === null) {
            $response->status(401);
            $response->body(json_encode(["status" => "error", "message" => "Неверный логин или пароль"]));
            return;
        }

        $response->header("Set-Cookie",
            \core\auth\Auth::COOKIE . "=" . $res["token"] . "; Path=/; HttpOnly; Max-Age=86400; SameSite=Lax");

        $response->body(json_encode([
            "status" => "ok",
            "user"   => ["id" => $res["user"]->id, "username" => $res["user"]->username],
        ]));
    }
}