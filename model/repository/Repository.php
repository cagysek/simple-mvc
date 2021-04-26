<?php

/**
 * Rodičovská třída pro třídy pracující s databází. Obsahuje často používané metody, které lze volat nad každými repository
 */

namespace App\Model\Repository;


use App\Service\DatabaseService;
use PDO;

class Repository
{

    protected string $tableName;

    private PDO $pdo;


    /**
     * Zsíká všechny záznamy z tabulky
     *
     * @return array
     */
    protected function findAll() : array
    {
        $statement = $this->getConnection()->prepare("SELECT * FROM " . $this->tableName);

        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Získá všechny záznamy z tabulky, které splňují podmínky
     *
     * @param array $data - zadané podmínky ve tvatu [sloupec => hodnota]
     * @return array
     */
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

    /**
     * Získání připojení k DB
     *
     * @return PDO
     */
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

    /**
     * Vloží pole do tabulky
     *
     * @param array $rows - data pro vložení
     * @param array $columns - definice sloupců, do kterých se bude vkládat
     */
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

    /**
     * Drop tabulky
     */
    public function dropTable() : void
    {
        $statement = $this->getConnection()->prepare("DROP TABLE IF EXISTS " . $this->tableName);
        $statement->execute();
    }

    /**
     * Kontrola existence tabulky
     *
     * @return bool
     */
    public function isTableExists() : bool
    {
        try
        {
            $result = $this->getConnection()->query("SELECT 1 FROM " . $this->tableName);
        }
        catch (\Throwable $e)
        {
            return FALSE;
        }

        return $result !== FALSE;
    }


}