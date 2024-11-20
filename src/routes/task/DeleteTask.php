<?php

namespace routes\task;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class DeleteTask extends AbstractRoute
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
        $result = $this->repository->removeTask($request->attribute("id"));
        $response->body(json_encode(["status" => $result ? "ok" : "error"]));
    }
}