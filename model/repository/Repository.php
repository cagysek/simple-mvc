<?php


namespace App\Model\Repository;


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

    protected function findBy(array $data) : array
    {
        $sql = "SELECT * FROM " . $this->tableName;

        $params = [];

        foreach ($data as $key => $value)
        {
            if (empty($params))
            {
                $sql .= " WHERE ";
            }
            else
            {
                $sql .= " AND ";
            }

            if (!empty($value))
            {
                $sql .= " `" . $key . "` = ?";
                $params[] = $value;
            }
            else
            {
                $sql .= " " . $key;
            }

        }

        $statement = $this->pdo->prepare($sql);

        $statement->execute($params);

        return $statement->fetchAll();
    }


}