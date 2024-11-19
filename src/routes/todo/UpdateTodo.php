<?php

namespace routes\todo;

use entities\todo\ToDoDraft;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class UpdateTodo extends AbstractRoute
{

    public function getPath(): string
    {
        return '/todos/{id}';
    }

    public function getMethod(): string
    {
        return 'PUT';
    }


    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        // var_dump($request->queryParameters()["title"]);

        if ($request->queryParameters()["title"] == null) {
            $body = $request->bodyStream()->readAll();
            $json = json_decode(json_decode($body));
            $title = $json->title;
            $done = $json->done;
            $cardId = $json->cardId;
        } else {
            $title = $request->queryParameters()["title"];
            $done = (bool)$request->queryParameters()["done"];
            $cardId = $request->queryParameters()["cardId"];
        }
        $response->header("Content-Type", "application/json");

        if (!is_numeric($cardId)) {
            var_dump($cardId);
            // todo logging input data
            $response->body(json_encode(["status" => "error"]));
            return;
        }


        $result = $this->repository->updateToDo(
            (int)$request->attribute("id"),
            new ToDoDraft($title, $done, $cardId)
        );

        $response->body(json_encode(["status" => $result ? "ok" : "error"]));
    }
}