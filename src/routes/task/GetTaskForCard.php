<?php

namespace routes\task;

use core\logger\Logger;
use php\http\{HttpServerRequest, HttpServerResponse};
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;

class GetTaskForCard extends AbstractRoute
{
    public function getPath(): string
    {
        return "/tasks/card/{id}";
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

        $result = $this->repository->getTaskForCard($request->attribute("id"));

        $response->body(json_encode(TaskMemoryRepository::toArray($result)));
    }
}