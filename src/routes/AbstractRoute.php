<?php

namespace routes;

use php\http\HttpServerRequest;
use php\http\HttpServerResponse;
use repository\todo\ToDoRepository;

abstract class AbstractRoute
{
    protected $repository;

    public function getPath(): string
    {
        return '/';
    }

    public function getMethod(): string
    {
        return 'GET';
    }

    public function __construct($repository = null)
    {
        $this->repository = $repository;
    }

    abstract public function __invoke(HttpServerRequest $request, HttpServerResponse $response);

    public function setRepository(ToDoRepository $repository)
    {
        $this->repository = $repository;
    }
}