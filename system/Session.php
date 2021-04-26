<?php

/**
 * Inicializuje se v bootstrapu, jediný úkol je nastartovat session
 */

namespace App\System;


class Session
{
    /**
     * Session constructor.
     */
    public function __construct()
    {
        session_start();
    }
}