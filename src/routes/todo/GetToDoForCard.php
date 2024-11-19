<?php

namespace routes\todo;

use core\logger\Logger;
use php\http\{HttpServerRequest, HttpServerResponse};
use repository\todo\ToDoMemoryRepository;
use routes\AbstractRoute;

class GetToDoForCard extends AbstractRoute
{
    public function getPath(): string
    {
        return "/todos/card/{id}";
    }

    public function getMethod(): string
    {
        return "GET";
    }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $response->header("Content-Type", "application/json");

        if (!is_numeric($request->attribute("id"))) {
            Logger::error("Id is not numeric");
            $response->body(json_encode(["status" => "error"]));
            return;
        }

        $result = $this->repository->getToDoForCard($request->attribute("id"));

        $response->body(json_encode(ToDoMemoryRepository::toArray($result)));
    }
}