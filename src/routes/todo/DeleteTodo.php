<?php

namespace routes\todo;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class DeleteTodo extends AbstractRoute
{

    public function getPath(): string
    {
        return '/todos/{id}';
    }

    public function getMethod(): string
    {
        return 'DELETE';
    }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $result = $this->repository->removeTodo($request->attribute("id"));
        $response->body(json_encode(["status" => $result ? "ok" : "error"]));
    }
}