<?php

namespace routes\card;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\AbstractRepository;
use routes\AuthRoute;
use repository\task\TaskMemoryRepository;

class GetAllCard extends AuthRoute
{
    public function getPath(): string { return "/cards"; }
    public function getMethod(): string { return "GET"; }

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->guard($request, $response);
        if ($user === null) return;

        $response->header("Content-Type", "application/json");
        $response->body(json_encode(
            AbstractRepository::toArray($this->repository->getAllCards($user->id)),
            JSON_UNESCAPED_UNICODE
        ));
    }
}