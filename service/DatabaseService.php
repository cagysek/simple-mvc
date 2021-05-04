<?php

/**
 * Service pro připojení do DB
 */


namespace App\Service;


use PDOException;

class DatabaseService
{
    public \PDO $pdo;

    private static ?\PDO $instance = NULL;

    private function __construct(){}

    public static function getInstance() : \PDO
    {
        if (!isset(self::$instance))
        {
            $config = include(__DIR__ .  '/../config/database.php');

            try
            {
                self::$instance = new \PDO('mysql:host=' . $config['host'] . ';dbname=' . $config['database'] . ';charset=utf8',
                    $config['username'],
                    $config['password'],);
            }
            catch (PDOException $ex)
            {
                die(json_encode(array('outcome' => false, 'message' => 'Unable to connect')));
            }
        }

        return self::$instance;
    }

}