<?php

namespace routes\card;

use entities\card\CardDraftData;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;
use routes\AuthRoute;

class UpdateCard extends AuthRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->guard($request, $response);
        if ($user === null) return;

        $body = $request->bodyStream()->readFully();
        $params = json_decode($body, true);
        if (!is_array($params)) {
            $params = $request->queryParameters();
        }

        $title   = $params["title"]   ?? null;
        $details = $params["details"] ?? null;

        if ($title === null && $details === null) {
            $response->status(400);
            $response->body(json_encode(["status" => "error", "message" => "Empty payload"]));
            return;
        }

        $ok = $this->repository->updateCard(
            (int)$request->attribute("id"),
            new \entities\card\CardDraftData((string)$title, (string)$details),
            $user->id
        );

        $response->header("Content-Type", "application/json");
        $response->body(json_encode(["status" => $ok ? "ok" : "error"]));
    }

    public function getPath(): string
    {
        return '/cards/{id}';
    }

    public function getMethod(): string
    {
        return "PUT";
    }
}