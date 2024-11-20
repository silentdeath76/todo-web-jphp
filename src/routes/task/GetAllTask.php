<?php

namespace routes\task;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;

class GetAllTask extends AbstractRoute
{
    public function getPath(): string
    {
        return "/tasks";
    }


    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $response->header("Content-Type", "application/json");
        $response->write(json_encode(TaskMemoryRepository::toArray($this->repository->getAllTask())));
    }
}