<?php

namespace routes\task;

use core\logger\Logger;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AuthRoute;
use repository\AbstractRepository;

class GetTaskForCard extends AuthRoute
{
    public function getPath(): string { return "/tasks/card/{id}"; }
    public function getMethod(): string { return "GET"; }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->guard($request, $response);
        if ($user === null) return;

        $id = $request->attribute("id");
        if (!is_numeric($id)) {
            $response->status(400);
            $response->header("Content-Type", "application/json");
            $response->body(json_encode(["status" => "error", "message" => "Invalid id"]));
            return;
        }

        $result = $this->repository->getTaskForCard((int)$id, $user->id);

        $response->header("Content-Type", "application/json");
        $response->body(json_encode(
            AbstractRepository::toArray($result),
            JSON_UNESCAPED_UNICODE
        ));
    }
}