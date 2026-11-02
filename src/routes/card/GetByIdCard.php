<?php

namespace routes\card;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;
use routes\AuthRoute;

class GetByIdCard extends AuthRoute
{
    public function getPath(): string
    {
        return "/cards/{id}";
    }

    public function getMethod(): string
    {
        return "GET";
    }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->guard($request, $response);
        if ($user === null) return;

        var_dump(__METHOD__);
        var_dump($request->queryParameters());
        var_dump($request);

        $id = $request->attribute('id');
        if (!is_numeric($id)) {
            $response->status(400);
            $response->body(json_encode(["status" => "error", "message" => "Invalid id"]));
            return;
        }

        $card = $this->repository->getCard((int)$id, $user->id);
        if ($card === null) {
            $response->status(404);
            $response->body(json_encode(["status" => "error", "message" => "Not Found"]));
            return;
        }

        $response->header("Content-Type", "application/json");
        $response->body(json_encode($card->toArray(), JSON_UNESCAPED_UNICODE));
    }
}