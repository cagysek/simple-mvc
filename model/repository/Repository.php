<?php


namespace App\Model\Repository;


use App\Service\DatabaseService;
use PDO;

class Repository
{

    protected string $tableName;

    private PDO $pdo;


    protected function findAll() : array
    {
        $statement = $this->getConnection()->prepare("SELECT * FROM " . $this->tableName);

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

        $statement = $this->getConnection()->prepare($sql);

        $statement->execute($params);

        return $statement->fetchAll();
    }

    protected function getConnection() : PDO
    {
        if (!isset($this->pdo))
        {
            $this->pdo = DatabaseService::getInstance();
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            return $this->pdo;
        }

        return $this->pdo;
    }

    protected function insertRows(array $rows, array $columns) : void
    {
        $sql = "
            INSERT INTO " . $this->tableName . " (" . implode(',', $columns) . ") VALUES
        ";

        $params = [];
        $inserts = [];
        foreach ($rows as $row)
        {
            $placeholder = [];
            foreach ($row as $column)
            {
                $params[] = $column;
                $placeholder[] = "?";
            }

            $inserts[] = "(" . implode(',', $placeholder) . ")";
        }

        $sql .= implode(',', $inserts);

        $statement = $this->getConnection()->prepare($sql);

        $statement->execute($params);
    }


}