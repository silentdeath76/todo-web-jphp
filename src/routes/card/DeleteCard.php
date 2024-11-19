<?php

namespace routes\card;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class DeleteCard extends AbstractRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $result = $this->repository->deleteCard($request->attribute("id"));
        $response->header("Content-Type", "application/json");
        $response->body(json_encode(["status" => $result ? "ok" : "error"]));
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