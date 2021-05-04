<?php

    require_once __DIR__ . '/vendor/autoload.php';

use App\Controller\BaseController;
use App\controller\HomeController;
use App\Controller\SettingsController;
use App\Controller\StudentController;
use App\controller\SummaryController;
use App\system\Bootstrap;
use App\system\Router;
use \App\system\Route;


    $app = new Bootstrap();

    // definice mapování url na akce
    $app->router->addRoute('/', Router::GET, new Route(HomeController::class, 'default'));
    $app->router->addRoute('/navod', Router::GET, new Route(HomeController::class, 'default'));
    $app->router->addRoute('/detail', Router::GET, new Route(HomeController::class, 'detail'));
    $app->router->addRoute('/celkove', Router::GET, new Route(SummaryController::class, 'default'));
    $app->router->addRoute('/login-student', Router::POST, new Route(BaseController::class, 'loginStudent'));
    $app->router->addRoute('/login-teacher', Router::POST, new Route(BaseController::class, 'loginTeacher'));
    $app->router->addRoute('/register-student', Router::POST, new Route(BaseController::class, 'registerStudent'));
    $app->router->addRoute('/register-teacher', Router::POST, new Route(BaseController::class, 'registerTeacher'));
    $app->router->addRoute('/logout', Router::POST, new Route(BaseController::class, 'logout'));
    $app->router->addRoute('/logout', Router::GET, new Route(BaseController::class, 'logout'));
    $app->router->addRoute('/change-password', Router::POST, new Route(BaseController::class, 'changePassword'));
    $app->router->addRoute('/osobniCislo', Router::GET, new Route(SummaryController::class, 'studentInfo'));
    $app->router->addRoute('/nastaveni', Router::GET, new Route(SettingsController::class, 'default'));
    $app->router->addRoute('/studenti', Router::GET, new Route(StudentController::class, 'list'));
    $app->router->addRoute('/select-student', Router::POST, new Route(StudentController::class, 'selectStudent'));
    $app->router->addRoute('/reset-database', Router::POST, new Route(SettingsController::class, 'resetDatabase'));
    $app->router->addRoute('/reset-student-password', Router::POST, new Route(SettingsController::class, 'resetStudentPassword'));
    $app->router->addRoute('/update-tasks', Router::POST, new Route(SettingsController::class, 'updateTasks'));
    $app->router->addRoute('/initialization', Router::POST, new Route(SettingsController::class, 'initialization'));


    $app->run();
?>