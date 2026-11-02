<?php

namespace routes\task;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;
use routes\AuthRoute;

class DeleteTask extends AuthRoute
{

    public function getPath(): string
    {
        return '/tasks/{id}';
    }

    public function getMethod(): string
    {
        return 'DELETE';
    }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->guard($request, $response);
        if ($user === null) return;

        $result = $this->repository->removeTask($request->attribute("id"), $user->id);
        $response->body(json_encode(["status" => $result ? "ok" : "error"]));
    }
}