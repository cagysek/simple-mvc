<?php


namespace App\Model;


use App\Service\DatabaseService;
use PDO;

class Repository
{

    protected string $tableName;

    protected PDO $pdo;

    /**
     * Repository constructor.
     *
     */
    public function __construct()
    {
        $this->pdo = DatabaseService::getInstance();
    }


    protected function findAll() : array
    {
        $statement = $this->pdo->prepare("SELECT * FROM " . $this->tableName);

        $statement->execute();

        return $statement->fetchAll();
    }


}