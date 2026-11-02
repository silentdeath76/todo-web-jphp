<?php

namespace routes\task;

use entities\task\TaskDraft;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;
use routes\AuthRoute;

class CreateTask extends AuthRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->guard($request, $response);
        if ($user === null) return;

        $raw = $request->bodyStream()->readFully();
        $params = json_decode($raw, true);

        // страховка от двойной сериализации
        if (is_string($params)) {
            $params = json_decode($params, true);
        }

        $response->header("Content-Type", "application/json");

        $title  = $params["title"]  ?? null;
        $cardId = $params["cardId"] ?? null;

        if ($title === null || $cardId === null || !is_numeric($cardId)) {
            $response->status(400);
            $response->body(json_encode([
                "status"  => "error",
                "message" => "title and cardId required",
                "received" => $params,
            ], JSON_UNESCAPED_UNICODE));
            return;
        }

        try {
            $task = $this->repository->addTask(
                new \entities\task\TaskDraft((string)$title, (bool)($params["done"] ?? false), (int)$cardId),
                $user->id
            );
        } catch (\Exception $e) {
            $response->status(403);
            $response->body(json_encode(["status" => "error", "message" => $e->getMessage()]));
            return;
        }

        $response->body(json_encode($task->toArray(), JSON_UNESCAPED_UNICODE));
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