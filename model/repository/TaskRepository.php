<?php


namespace App\Model\Repository;


class TaskRepository extends Repository
{
    const TABLE_NAME = 'task';

    protected string $tableName = self::TABLE_NAME;

}