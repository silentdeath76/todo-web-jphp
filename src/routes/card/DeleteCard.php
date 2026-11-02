<?php

namespace routes\card;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;
use routes\AuthRoute;

class DeleteCard extends AuthRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        // 1. Сначала авторизация (guard не читает тело, так что порядок не важен)
        $user = $this->guard($request, $response);
        if ($user === null) return;

        // 2. Один раз читаем тело
        $raw = $request->bodyStream()->readFully();
        $params = json_decode($raw, true);

        // страховка от двойной сериализации (на случай других мест)
        if (is_string($params)) {
            $params = json_decode($params, true);
        }

        $ok = $this->repository->deleteCard((int)$request->attribute("id"), $user->id);

        $response->header("Content-Type", "application/json");
        $response->body(json_encode(["status" => $ok ? "ok" : "error"]));
    }

    public function getPath(): string
    {
        return "/cards/{id}";
    }

    public function getMethod(): string
    {
        return "DELETE";
    }
}