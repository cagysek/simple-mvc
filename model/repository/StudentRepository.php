<?php


namespace App\Model\Repository;


class StudentRepository extends Repository
{
    const TABLE_NAME = 'student';

    protected string $tableName = self::TABLE_NAME;

    const COL_ID = 'id';
    const COL_SCHOOL_NUMBER = 'school_number';
    const COL_NAME = 'name';
    const COL_LASTNAME = 'lastname';
    const COL_PASSWORD = 'password';

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

    public function getStudentsSchoolNumbersWithoutPassword() : array
    {
        $rows = $this->findBy([self::COL_PASSWORD . ' IS NULL' => ""]);

        return $this->getSchoolNumbers($rows);
    }

    public function getStudentSchoolNumbersWithPassword() : array
    {
        $rows =  $this->findBy([self::COL_PASSWORD . ' IS NOT NULL' => ""]);

        return $this->getSchoolNumbers($rows);
    }

    private function getSchoolNumbers(array $rows) : array
    {
        $schoolNumbers = [];

        foreach ($rows as $row)
        {
            $schoolNumbers[] = $row[self::COL_SCHOOL_NUMBER];
        }

        return  $schoolNumbers;
    }

    public function updateStudentPassword(string $schoolNumber, string $password) : void
    {
        $statement = $this->pdo->prepare('UPDATE student SET `password` = ? WHERE `school_number` = ?');

        $statement->execute([$password, $schoolNumber]);
    }

    public function getStudentPassword(string $schoolNumber) : ?string
    {
        $data = $this->findBy([self::COL_SCHOOL_NUMBER => $schoolNumber]);

        if (empty($data))
        {
            return NULL;
        }

        return $data[0][self::COL_PASSWORD];
    }

    public function getStudentBySchoolNumber(string $schoolNumber) : ?array
    {
        $student = $this->findBy([self::COL_SCHOOL_NUMBER => $schoolNumber]);

        if (empty($student))
        {
            return NULL;
        }

        return $student[0];
    }
}