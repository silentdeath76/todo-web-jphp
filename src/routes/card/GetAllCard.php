<?php

namespace routes\card;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\todo\ToDoMemoryRepository;
use routes\AbstractRoute;

class GetAllCard extends AbstractRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $response->header("Content-Type", "application/json");
        $response->body(json_encode(ToDoMemoryRepository::toArray($this->repository->getAllCards()),  JSON_UNESCAPED_UNICODE));
    }

    public function getMethod(): string
    {
        return "GET";
    }

    public function getPath(): string
    {
        return "/cards";
    }
}