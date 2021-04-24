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
    $app->router->addRoute('/public/logout', Router::POST, new Route(\App\controller\BaseController::class, 'logout'));
    $app->router->addRoute('/public/logout', Router::GET, new Route(\App\controller\BaseController::class, 'logout'));
    $app->router->addRoute('/public/change-password', Router::POST, new Route(\App\controller\BaseController::class, 'changePassword'));
    $app->router->addRoute('/public/osobniCislo', Router::GET, new Route(\App\controller\SummaryController::class, 'studentInfo'));
    $app->router->addRoute('/public/nastaveni', Router::GET, new Route(\App\controller\SettingsController::class, 'default'));
    $app->router->addRoute('/public/studenti', Router::GET, new Route(\App\controller\StudentController::class, 'list'));
    $app->router->addRoute('/public/select-student', Router::POST, new Route(\App\controller\StudentController::class, 'selectStudent'));
    $app->router->addRoute('/public/reset-database', Router::POST, new Route(\App\controller\SettingsController::class, 'resetDatabase'));
    $app->router->addRoute('/public/reset-student-password', Router::POST, new Route(\App\controller\SettingsController::class, 'resetStudentPassword'));
    $app->router->addRoute('/public/update-tasks', Router::POST, new Route(\App\controller\SettingsController::class, 'updateTasks'));
    $app->router->addRoute('/public/initialization', Router::POST, new Route(\App\controller\SettingsController::class, 'initialization'));


    $app->run();
?>