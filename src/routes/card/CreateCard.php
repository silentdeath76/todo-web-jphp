<?php

namespace routes\card;

use entities\card\CardDraftData;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AuthRoute;
use repository\AbstractRepository;

class CreateCard extends AuthRoute
{
    public function getPath(): string { return "/cards"; }
    public function getMethod(): string { return "POST"; }

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

        $response->header("Content-Type", "application/json");

        if (!is_array($params) || !isset($params["title"], $params["details"])) {
            $response->status(400);
            $response->body(json_encode([
                "status"  => "error",
                "message" => "Invalid payload",
                "received" => $raw,
            ], JSON_UNESCAPED_UNICODE));
            return;
        }

        // 3. Создаём карточку с привязкой к пользователю
        $card = $this->repository->addCard(
            new CardDraftData((string)$params["title"], (string)$params["details"]),
            $user->id
        );

        if ($card === null) {
            $response->status(500);
            $response->body(json_encode(["status" => "error", "message" => "Cannot create card"]));
            return;
        }

        // 4. Сериализуем через AbstractRepository, а не TaskMemoryRepository
        $response->body(json_encode(
            AbstractRepository::toArray($card),
            JSON_UNESCAPED_UNICODE
        ));
    }
}