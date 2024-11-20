<?php

namespace routes\task;

use entities\task\TaskDraft;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;

class CreateTask extends AbstractRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $params = json_decode(json_decode($request->bodyStream()->readFully()), true);

        $todo = $this->repository->addTask(new TaskDraft($params["title"], (bool)$params["done"] ?? false, $params["id"]));

        $response->header("Content-Type", "application/json");
        $response->body(json_encode(TaskMemoryRepository::toArray($todo)));
    }

    public function getPath(): string
    {
        return "/tasks";
    }

    public function getMethod(): string
    {
        return "POST";
    }
}