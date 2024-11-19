<?php

namespace routes\todo;

use entities\todo\ToDoDraft;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\todo\ToDoMemoryRepository;
use routes\AbstractRoute;

class CreateTodo extends AbstractRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $params = json_decode(json_decode($request->bodyStream()->readFully()), true);

        $todo = $this->repository->addToDo(new ToDoDraft($params["title"], (bool)$params["done"] ?? false, $params["id"]));

        $response->header("Content-Type", "application/json");
        $response->body(json_encode(ToDoMemoryRepository::toArray($todo)));
    }

    public function getPath(): string
    {
        return "/todos";
    }

    public function getMethod(): string
    {
        return "POST";
    }
}