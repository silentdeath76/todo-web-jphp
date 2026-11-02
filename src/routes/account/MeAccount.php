<?php

namespace routes\account;

use core\auth\Auth;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class MeAccount extends AbstractRoute
{
    private $auth;

    public function __construct(Auth $auth) { $this->auth = $auth; }

    public function getPath(): string { return "/account/me"; }
    public function getMethod(): string { return "GET"; }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->auth->user($request);
        $response->header("Content-Type", "application/json");
        if ($user === null) {
            $response->status(401);
            $response->body(json_encode(["status" => "error", "message" => "Not authenticated"]));
            return;
        }
        $response->body(json_encode([
            "status" => "ok",
            "user"   => ["id" => $user->id, "username" => $user->username, "role" => $user->role],
        ]));
    }
}