<?php

namespace routes\task;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;

class GetByIdTask extends AbstractRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $id = $request->attribute('id');

        if (!is_numeric($id)) {
            $response->write('Invalid id');
            return;
        }

        $todo = $this->repository->getTask($id);

        if ($todo === null) {
            $response->status(404, 'Not Found');
            return;
        }

        $response->write(json_encode(TaskMemoryRepository::toArray($todo)));
    }

    public function getPath(): string
    {
        return "/tasks/{id}";
    }
}