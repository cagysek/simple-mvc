<?php

/**
 * Třída představující hlavní tělo programu
 *
 */

namespace App\System;

use App\Enum\EStatusCode;

class Bootstrap
{

    private Request $request;

    public Router $router;

    public Session $session;

    private array $environmentParams;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
        $this->router = new Router($this->request, $this);
        $this->session = new Session();
        $this->environmentParams = include(__DIR__ . './../config/env.php');

    }

    /**
     * Zpracování požadavku
     */
    public function run() : void
    {
        try
        {
            // převod requestu na správnou akci
            $response = $this->router->resolve();

            // na základě response provedení akce
            if ($response->getStatusCode() == EStatusCode::SUCCESS)
            {
                $response->send();
            }
            elseif ($response->getStatusCode() == EStatusCode::REDIRECT)
            {
                $location = $response->getRedirect();
                $baseUri = $this->environmentParams['path_to_root'];

                header("Location: " . $baseUri . $location);
            }
            else if ($response->getStatusCode() == EStatusCode::INTERNAL_ERROR)
            {
                echo "ERROR:";
                echo "<br>";
                echo $response->getBody();
            }
            else
            {
                echo "Hups";
            }

        }
        catch (\Throwable $e)
        {
            echo "ERROR: ";
            echo "<br>";
            echo $e->getMessage();
        }
    }
}