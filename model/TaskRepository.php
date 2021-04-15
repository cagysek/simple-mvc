<?php


namespace App\Model;


class TaskRepository extends Repository
{
    const TABLE_NAME = 'task';

    protected string $tableName = self::TABLE_NAME;

}