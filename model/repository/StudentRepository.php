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
    const COL_FIRSTNAME = 'firstname';
    const COL_LASTNAME = 'lastname';
    const COL_PASSWORD = 'password';


    /**
     * Vrací celkový počet studentů
     *
     * @return int
     */
    public function getTotalStudentsCount() : int
    {
        $students = $this->findAll();

        return count($students);
    }

    /**
     * Vrací seznam školních číssel, které nemají nastavené heslo
     *
     * @return array
     */
    public function getStudentsSchoolNumbersWithoutPassword() : array
    {
        $rows = $this->findBy([self::COL_PASSWORD . ' IS NULL' => ""]);

        return $this->getSchoolNumbers($rows);
    }

    /**
     * Vrací seznam školních čísel, které mají nastavené heslo
     *
     * @return array
     */
    public function getStudentSchoolNumbersWithPassword() : array
    {
        $rows =  $this->findBy([self::COL_PASSWORD . ' IS NOT NULL' => ""]);

        return $this->getSchoolNumbers($rows);
    }

    /**
     * Zsíká z pole všechyn školní čísla
     *
     * @param array $rows
     * @return array
     */
    private function getSchoolNumbers(array $rows) : array
    {
        $schoolNumbers = [];

        foreach ($rows as $row)
        {
            $schoolNumbers[] = $row[self::COL_SCHOOL_NUMBER];
        }

        return  $schoolNumbers;
    }

    /**
     * Updatuje heslo studenta podle školního čísla
     *
     * @param string $schoolNumber
     * @param string $password
     */
    public function updateStudentPassword(string $schoolNumber, string $password) : void
    {
        $statement = $this->getConnection()->prepare('UPDATE student SET `password` = ? WHERE `school_number` = ?');

        $statement->execute([$password, $schoolNumber]);
    }

    /**
     * Zsíká heslo studenta na základě školního čísla
     *
     * @param string $schoolNumber
     * @return string|null
     */
    public function getStudentPassword(string $schoolNumber) : ?string
    {
        $data = $this->findBy([self::COL_SCHOOL_NUMBER => $schoolNumber]);

        if (empty($data))
        {
            return NULL;
        }

        return $data[0][self::COL_PASSWORD];
    }

    /**
     * Zsíká studenta na základě školního čísla
     *
     * @param string $schoolNumber
     * @return array|null
     */
    public function getStudentBySchoolNumber(string $schoolNumber) : ?array
    {
        $student = $this->findBy([self::COL_SCHOOL_NUMBER => $schoolNumber]);

        if (empty($student))
        {
            return NULL;
        }

        return $student[0];
    }

    /**
     * Zsíká studenta na základě ID
     *
     * @param int $id
     * @return mixed|null
     */
    public function getStudentById(int $id)
    {
        $student = $this->findBy([self::COL_ID => $id]);

        if (empty($student))
        {
            return NULL;
        }

        return $student[0];
    }

    /**
     * Získá informace pro výpis studentů
     *
     * @param int $taskCount - číslo aktuálně odevzaných úloh (maximální odevzdané číslo úkolu mezi všemi studenty)
     * @return array
     */
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

    /**
     * Vrací seznam studentů s nastaveným heslem
     *
     * @return array
     */
    public function getStudentsWithPassword() : array
    {
        return $this->findBy([self::COL_PASSWORD . ' IS NOT NULL' => ""]);
    }

    /**
     * Resetuje studentovo heslo
     *
     * @param string $schoolNumber
     */
    public function resetStudentPassword(string $schoolNumber) : void
    {
        $sql = "
            UPDATE student SET password = NULL WHERE school_number = ?
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute([$schoolNumber]);
    }

    /**
     * Vloží studenty do DB
     *
     * @param array $data
     */
    public function insertStudents(array $data) : void
    {
        $this->insertRows($data, [self::COL_SCHOOL_NUMBER, self::COL_FIRSTNAME, self::COL_LASTNAME]);
    }

    /**
     * Vrací mapu školní číslo => id
     *
     * @return array
     */
    public function getStudentSchoolNumberIdMap() : array
    {
        $sql = "
            SELECT school_number, id FROM student;
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute();

        return $statement->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * Init tabulky
     */
    public function createTable() : void
    {
        $sql = "
            CREATE TABLE `student` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `school_number` varchar(255) DEFAULT NULL,
                `firstname` varchar(255) DEFAULT NULL,
                `lastname` varchar(255) DEFAULT NULL,
                `password` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute();
    }
}