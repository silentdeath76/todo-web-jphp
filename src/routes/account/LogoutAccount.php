<?php

namespace routes\account;

use core\auth\Auth;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class LogoutAccount extends AbstractRoute
{
    private $auth;

    public function __construct(Auth $auth) { $this->auth = $auth; }

    public function getPath(): string { return "/account/logout"; }
    public function getMethod(): string { return "POST"; }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $cookies = $request->cookies();
        $cookie = $cookies[Auth::COOKIE] ?? null;

        $token = null;
        if (is_array($cookie)) {
            $token = $cookie["value"] ?? null;
        } else if (is_object($cookie)) {
            $token = $cookie->value ?? null;
        } else if (is_string($cookie)) {
            $token = $cookie;
        }

        if ($token) {
            $this->auth->logout($token);
        }

        // Set-Cookie через header, а не $response->cookie()
        $response->header("Set-Cookie",
            Auth::COOKIE . "=; Path=/; HttpOnly; Max-Age=0; SameSite=Lax");
        $response->header("Content-Type", "application/json");
        $response->body(json_encode(["status" => "ok"]));
    }
}