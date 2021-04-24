<?php


namespace App\Model\Repository;


use App\Enum\EStatusCode;
use App\System\Request;
use App\System\Response;

class StudentRepository extends Repository
{
    const TABLE_NAME = 'student';

    protected string $tableName = self::TABLE_NAME;

    const COL_ID = 'id';
    const COL_SCHOOL_NUMBER = 'school_number';
    const COL_NAME = 'name';
    const COL_LASTNAME = 'lastname';
    const COL_PASSWORD = 'password';


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
        $statement = $this->getConnection()->prepare('UPDATE student SET `password` = ? WHERE `school_number` = ?');

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

    public function getStudentById(int $id)
    {
        $student = $this->findBy([self::COL_ID => $id]);

        if (empty($student))
        {
            return NULL;
        }

        return $student[0];
    }

    public function getStudentListData(int $taskCount) : array
    {
        $sql = "
            SELECT s.id as id,s.lastname as lastname, s.firstname as firstname, s.`school_number` as schoolNumber, suc.success_attempts as successAttempts
        ";

        for ($i = 1 ; $i <= $taskCount ; $i++)
        {
            $sql .= ", task_" . $i . ".totalAttempts as totalAttempts" . $i . ", task_" . $i . ".is_ok as isOk" . $i;
        }

        $sql .= " 
            FROM student s
            LEFT JOIN (SELECT SUM(CASE WHEN `result` = 1 THEN 1 END) as success_attempts, student_id FROM task GROUP by `student_id`) AS suc ON suc.student_id = s.id
        ";

        for ($i = 1 ; $i <= $taskCount ; $i++)
        {
            $sql .= " LEFT JOIN (
                        SELECT 
                        COUNT(*) as totalAttempts, 
                        SUM(CASE WHEN t.result = 1 THEN 1 END) AS is_ok, 
                        t.student_id FROM task t WHERE `name` = " . $i . " GROUP BY student_id
                    ) as task_" . $i . " ON task_" . $i . ".student_id = s.id";
        }

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute();

        return $statement->fetchAll();
    }

    public function getStudentsWithPassword() : array
    {
        return $this->findBy([self::COL_PASSWORD . ' IS NOT NULL' => ""]);
    }

    public function resetStudentPassword(string $schoolNumber) : void
    {
        $sql = "
            UPDATE student SET password = NULL WHERE school_number = ?
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute([$schoolNumber]);
    }
}