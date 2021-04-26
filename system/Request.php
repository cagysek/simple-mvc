<?php

/**
 * Třída představující request, obsahuje všechny potřebné metody, pokud je potřeba je možné v akci k requestu přistupovat
 */

namespace App\System;


class Request
{
    public function getMethod() : string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getUrl() : string
    {
        $path = $_SERVER['REQUEST_URI'];
        $position = strpos($path, '?');
        if ($position !== false) {
            $path = substr($path, 0, $position);
        }
        return $path;
    }

    public function getReferer() : string
    {
        return $_SERVER['HTTP_REFERER'];
    }

    public function isGet() : bool
    {
        return $this->getMethod() === 'get';
    }

    public function isPost() : bool
    {
        return $this->getMethod() === 'post';
    }

    public function getBody() : array
    {
        $data = [];
        if ($this->isGet()) {
            foreach ($_GET as $key => $value) {
                $data[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->isPost()) {
            foreach ($_POST as $key => $value) {
                $data[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $data;
    }
}