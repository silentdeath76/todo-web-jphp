<?php

namespace routes\todo;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\todo\ToDoMemoryRepository;
use routes\AbstractRoute;

class GetAllTodos extends AbstractRoute
{
    public function getPath(): string
    {
        return "/todos";
    }


    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $response->header("Content-Type", "application/json");
        $response->write(json_encode(ToDoMemoryRepository::toArray($this->repository->getAllToDos())));
    }
}