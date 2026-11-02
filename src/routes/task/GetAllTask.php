<?php

namespace routes\task;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\AbstractRepository;
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;
use routes\AuthRoute;

class GetAllTask extends AuthRoute
{
    public function getPath(): string
    {
        return "/tasks";
    }


    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->guard($request, $response);
        if ($user === null) return;

        $response->header("Content-Type", "application/json");
        $response->write(json_encode(AbstractRepository::toArray($this->repository->getAllTask())));
    }
}