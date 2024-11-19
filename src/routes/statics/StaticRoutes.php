<?php

namespace routes\statics;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use php\lib\fs;
use routes\AbstractRoute;

class StaticRoutes extends AbstractRoute
{

    public function __invoke(HttpServerRequest $request, HttpServerResponse $response)
    {
        $path = 'res://view' . str_replace('/..', '/', $request->path());
        switch (fs::ext($path)) {
            case 'css':
                $mime = "text/css";
                break;
            case 'js':
                $mime = "text/javascript";
                break;
            case 'png':
                $mime = "image/png";
                break;
            default:
                $mime = "text/text";
        }

        $response->header("ContentType", $mime);
        $response->body(file_get_contents($path));
    }

    public function getPath(): string
    {
        return "/static/**";
    }
}