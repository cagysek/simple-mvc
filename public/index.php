<?php

    require_once __DIR__ . '/../vendor/autoload.php';

    use App\controller\HomeController;
    use App\controller\SummaryController;
    use App\system\Bootstrap;
    use App\system\Router;
    use \App\system\Route;


    $app = new Bootstrap();

    $app->router->addRoute('/public/', Router::GET, new Route(HomeController::class, 'default'));
    $app->router->addRoute('/public/navod', Router::GET, new Route(HomeController::class, 'default'));
    $app->router->addRoute('/public/detail', Router::GET, new Route(HomeController::class, 'detail'));
    $app->router->addRoute('/public/celkove', Router::GET, new Route(SummaryController::class, 'default'));
    $app->router->addRoute('/public/login-student', Router::POST, new Route(\App\controller\BaseController::class, 'loginStudent'));
    $app->router->addRoute('/public/login-teacher', Router::POST, new Route(\App\controller\BaseController::class, 'loginTeacher'));
    $app->router->addRoute('/public/register-student', Router::POST, new Route(\App\controller\BaseController::class, 'registerStudent'));
    $app->router->addRoute('/public/register-teacher', Router::POST, new Route(\App\controller\BaseController::class, 'registerTeacher'));


    $app->run();
?>