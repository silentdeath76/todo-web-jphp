<?php

namespace routes\card;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;

class GetByIdCard extends AbstractRoute
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
        $id = $request->attribute('id');

        if (!is_numeric($id)) {
            $response->write('Invalid id');
            return;
        }

        $todo = $this->repository->getCard($id);

        if ($todo === null) {
            $response->status(404, 'Not Found');
            return;
        }

        $response->header("Content-Type", "application/json");
        $response->write(json_encode(TaskMemoryRepository::toArray($todo)));
    }
}