<?php

namespace routes\card;

use entities\card\CardDraftData;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class UpdateCard extends AbstractRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $params = $request->queryParameters();

        if ($params["title"] == null && $params["details"] == null) {
            $body = $request->bodyStream()->readAll();
            $json = json_decode(json_decode($body));
            $title = $json->title;
            $details = $json->details;
        } else {
            $title = $params["title"];
            $details = $params["details"];
        }


        $result = $this->repository->updateCard(
            (int)$request->attribute("id"),
            new CardDraftData($title, $details)
        );

        $response->header("Content-Type", "application/json");
        $response->body(json_encode(["status" => $result ? "ok" : "error"]));
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