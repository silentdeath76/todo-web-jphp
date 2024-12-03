<?php

namespace routes\task;

use core\logger\Logger;
use entities\task\TaskDraft;
use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use routes\AbstractRoute;

class UpdateTask extends AbstractRoute
{

    public function getPath(): string
    {
        return '/tasks/{id}';
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
            $json = json_decode($body);
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
            Logger::info(sprintf("Received \$cardId: %s", $cardId));
            $response->body(json_encode(["status" => "error"]));
            return;
        }


        $result = $this->repository->updateTask(
            (int)$request->attribute("id"),
            new TaskDraft($title, $done, $cardId)
        );

        $response->body(json_encode(["status" => $result ? "ok" : "error"]));
    }
}