<?php

namespace routes\todo;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\todo\ToDoMemoryRepository;
use routes\AbstractRoute;

class GetByIdTodos extends AbstractRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $id = $request->attribute('id');

        if (!is_numeric($id)) {
            $response->write('Invalid id');
            return;
        }

        $todo = $this->repository->getToDo($id);

        if ($todo === null) {
            $response->status(404, 'Not Found');
            return;
        }

        $response->write(json_encode(ToDoMemoryRepository::toArray($todo)));
    }

    public function getPath(): string
    {
        return "/todos/{id}";
    }
}