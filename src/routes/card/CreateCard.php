<?php

namespace routes\card;

use entities\card\CardDraftData;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;

class CreateCard extends AbstractRoute
{
    public function getPath(): string
    {
        return "/cards";
    }

    public function getMethod(): string
    {
        return "POST";
    }


    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $params = json_decode(json_decode($request->bodyStream()->readFully()), true);

        $response->header("Content-Type", "application/json");
        if (count($params) < 2) {
            $response->body(json_encode(["status" => "error"]));
            return;
        }

        $card = $this->repository->addCard(new CardDraftData($params["title"], $params["details"]));

        $response->body(json_encode(TaskMemoryRepository::toArray($card)));
    }
}