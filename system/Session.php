<?php


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