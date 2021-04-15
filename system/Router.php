<?php


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
    }


    /**
     * @param string $url
     * @param string $type
     * @param Route  $route
     */
    public function addRoute(string $url, string $type, Route $route) : void
    {
        $this->routeMap[$type][$url] = $route;
    }

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

        return call_user_func_array($newCallback, ['a' => "ab"]);
    }
}