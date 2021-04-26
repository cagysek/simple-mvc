<?php

/**
 * Service pro inicializaci twigu
 */

namespace App\Service;


use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class TwigService
{
    private static ?Environment $instance = NULL;

    private function __construct(){}


    public static function getInstance() : Environment
    {
        if (!isset(self::$instance))
        {
            $twigConfig = include("../config/twig.php");

            $loader = new FilesystemLoader($twigConfig['root_folder']);

            self::$instance = new Environment($loader);
        }

        return self::$instance;
    }
}