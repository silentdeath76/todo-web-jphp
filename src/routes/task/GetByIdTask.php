<?php

namespace routes\task;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\AbstractRepository;
use repository\task\TaskMemoryRepository;
use routes\AbstractRoute;
use routes\AuthRoute;

class GetByIdTask  extends AuthRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $user = $this->guard($request, $response);
        if ($user === null) return;


        $id = $request->attribute('id');

        var_dump(__METHOD__);
        var_dump($request->queryParameters());

        if (!is_numeric($id)) {
            $response->write('Invalid id');
            return;
        }

        $todo = $this->repository->getTask($id);

        if ($todo === null) {
            $response->status(404, 'Not Found');
            return;
        }

        $response->write(json_encode(AbstractRepository::toArray($todo)));
    }

    public function getPath(): string
    {
        return "/tasks/{id}";
    }
}