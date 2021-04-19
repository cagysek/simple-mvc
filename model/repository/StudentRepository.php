<?php


namespace App\Model\Repository;


class StudentRepository extends Repository
{
    const TABLE_NAME = 'student';

    protected string $tableName = self::TABLE_NAME;

    /**
     * StudentRepository constructor.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getTotalStudentsCount() : int
    {
        $students = $this->findAll();

        return count($students);
    }


}