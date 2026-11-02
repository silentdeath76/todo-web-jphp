<?php

namespace routes;

use core\auth\Auth;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;

abstract class AuthRoute extends AbstractRoute
{
    /** @var Auth */
    protected $auth;

    public function __construct($repository, Auth $auth)
    {
        parent::__construct($repository);
        $this->auth = $auth;
    }

    protected function guard(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->auth->user($request);
        if ($user === null) {
            $response->status(401);
            $response->header("Content-Type", "application/json");
            $response->body(json_encode(["status" => "error", "message" => "Unauthorized"]));
            return null;
        }
        return $user;
    }
}