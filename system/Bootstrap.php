<?php

namespace App\System;

use App\Enum\EStatusCode;

class Bootstrap
{

    private Request $request;

    public Router $router;

    public Session $session;

    /**
     * App constructor.
     */
    public function __construct()
    {
        $this->request = new Request();
        $this->router = new Router($this->request, $this);
        $this->session = new Session();
    }


    public function run() : void
    {
        try
        {
            $response = $this->router->resolve();

            if ($response->getStatusCode() == EStatusCode::SUCCESS)
            {
                $response->send();
            }
            else
            {
                echo "error";
            }

        }
        catch (\Throwable $e)
        {
            echo "error: ";
            echo $e->getMessage();
        }
    }
}