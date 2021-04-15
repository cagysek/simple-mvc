<?php

    require_once __DIR__ . '/../vendor/autoload.php';

    use App\Controller\HomeController;
    use App\Controller\SummaryController;
    use App\system\Bootstrap;
    use App\system\Router;
    use \App\system\Route;


    $app = new Bootstrap();

    $app->router->addRoute('/public/', Router::GET, new Route(HomeController::class, 'default'));
    $app->router->addRoute('/public/navod', Router::GET, new Route(HomeController::class, 'default'));
    $app->router->addRoute('/public/detail', Router::GET, new Route(HomeController::class, 'detail'));
    $app->router->addRoute('/public/celkove', Router::GET, new Route(SummaryController::class, 'default'));


    $app->run();
?>