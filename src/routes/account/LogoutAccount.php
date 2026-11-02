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
        $token = is_array($cookie) ? ($cookie["value"] ?? null) : (is_object($cookie) ? ($cookie->value ?? null) : $cookie);

        if ($token) $this->auth->logout($token);
        $response->cookie(Auth::COOKIE, "", ["path" => "/", "maxAge" => 0]);
        $response->body(json_encode(["status" => "ok"]));
    }
}