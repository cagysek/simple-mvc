<?php

/**
 * Obsahuje všechno routování na základě url hledá vhodnou akci
 */

namespace App\System;


class Router
{
    private array $routeMap = [];

    private Request $request;

    private Response $response;

    const GET = 'get';
    const POST = 'post';
    /**
     * @var Bootstrap
     */
    private Bootstrap $bootstrap;

    private array $environmentParams;

    /**
     * Router constructor.
     *
     * @param Request   $request
     * @param Bootstrap $bootstrap
     */
    public function __construct(Request $request, Bootstrap $bootstrap)
    {
        $this->request = $request;
        $this->bootstrap = $bootstrap;

        $this->environmentParams = include(__DIR__ . './../config/env.php');
    }


    /**
     * Přidá routu do mapy
     *
     * @param string $url
     * @param string $type
     * @param Route  $route
     */
    public function addRoute(string $url, string $type, Route $route) : void
    {
        $url = $this->environmentParams['path_to_root'] . $url;

        $this->routeMap[$type][$url] = $route;
    }

    /**
     * Na základě url zjisti route a zavolá příslušnou akci
     *
     * @return Response
     * @throws \Exception
     */
    public function resolve() : Response
    {
        $method = $this->request->getMethod();
        $url = $this->request->getUrl();

        /** @var Route $route */
        $route = $this->routeMap[$method][$url] ?? false;

        if (!$route)
        {
            throw new \Exception("Page not Found");
        }

        $controller = $route->getController();
        $action = $route->getAction();

        $newCallback = [new $controller, $action];

        return call_user_func_array($newCallback, [$this->request]);
    }
}