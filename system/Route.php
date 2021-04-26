<?php

/**
 * Jednotlivé routování url na akce
 */

namespace App\System;


class Route
{
    private string $controller;

    private string $action;

    /**
     * Callback constructor.
     *
     * @param string $controller
     * @param string $action
     */
    public function __construct(string $controller, string $action)
    {
        $this->controller = $controller;
        $this->action = $action;
    }

    public function getController() : string
    {
        return $this->controller;
    }

    public function getAction() : string
    {
        return 'action' . ucfirst($this->action);
    }


}