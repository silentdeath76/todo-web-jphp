<?php

use routes\AbstractRoute;

class Routes
{
    public $routes = [];

    public function register($route)
    {
        $this->routes[] = $route;
    }

    /**
     * @return AbstractRoute []
     */
    public function getAllRoutes(): array
    {
        return $this->routes;
    }
}