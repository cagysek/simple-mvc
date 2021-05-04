<?php


namespace App\Model\Repository;


class TaskRepository extends Repository
{
    const TABLE_NAME = 'task';

    protected string $tableName = self::TABLE_NAME;

    const COL_ID = 'id';
    const COL_NAME = 'name';
    const COL_STUDENT_ID = 'student_id';
    const COL_SUBMITTED = 'submitted';
    const COL_RESULT = 'result';

    /**
     * Získání dat pro detail studenta
     *
     * @param int $studentId
     * @return array
     */
    public function getStudentTasksSummary(int $studentId) : array
    {
        $sql = "
            SELECT 
                `name`,
                COUNT(*) as total,
                MAX(`submitted`) as end_date, 
                MIN(`submitted`) as start_date,  
                SUM(CASE WHEN `result` = 0 THEN 1 END) as unsuccessful_attempt, 
                SUM(CASE WHEN `result` = 1 THEN 1 END) as successful_attempt,
                (SUM(CASE WHEN `result` = 1 THEN 1 END) / count(*)) as success_rate
            FROM task 
            WHERE `student_id` = ?
            GROUP by `name`
            ORDER by `name`;
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute([$studentId]);

        return $statement->fetchAll();
    }

    /**
     * Získání celkových dat pro detail studenta
     *
     * @param int $studentId
     * @return array
     */
    public function getStudentTasksTotalSummary(int $studentId) : array
    {
        $sql = "
            SELECT 
                count(*) as total,
                MAX(`submitted`) as end_date, 
                MIN(`submitted`) as start_date,  
                SUM(CASE WHEN `result` = 0 THEN 1 END) as unsuccessful_attempt, 
                SUM(CASE WHEN `result` = 1 THEN 1 END) as successful_attempt,
                (SUM(CASE WHEN `result` = 1 THEN 1 END) / count(*)) as success_rate
            FROM task 
            WHERE `student_id` = ?
            GROUP by `student_id`
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute([$studentId]);

        return $statement->fetchAll();
    }


    /**
     * Získání čísla maximálního odevzdaného úkolu
     *
     * @return int
     */
    public function getMaxTaskNumber() : int
    {
        $sql = "
            SELECT `name`
            FROM task
            GROUP BY `name`
            ORDER BY `name` DESC 
            LIMIT 1;
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute();

        $result = $statement->fetch();

        if (!$result)
        {
            return 0;
        }

        return $result[self::COL_NAME];
    }

    /**
     * Zsíkání dat pro progress bar na detailu studenta
     *
     * @param int $studentId
     * @return array
     */
    public function getProgressBarData(int $studentId) : array
    {
        $sql = "
            SELECT
                SUM(CASE WHEN `result` = 0 THEN 1 END) as unsuccessful, 
                SUM(CASE WHEN `result` = 1 THEN 1 END) as successful
            FROM task 
            WHERE `student_id` = ?
            GROUP by `name`
            ORDER by `name`;
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute([$studentId]);

        return $statement->fetchAll();
    }

    /**
     * Získání počtu odevzdání pro studenta na hodiny
     *
     * @param int $studentId
     * @return array
     */
    public function getStudentAttemptsTimes(int $studentId) : array
    {
        $sql = "
            SELECT 
			EXTRACT(HOUR FROM submitted) as `hour`,
            count(*) as `count`
            FROM task 
            WHERE `student_id` = ?
            GROUP by EXTRACT(HOUR FROM submitted)
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute([$studentId]);

        return $statement->fetchAll();
    }

    /**
     * Získání odevzdání na hodiny pro všechny studenty
     *
     * @return array
     */
    public function getAttemptsTimes() : array
    {
        $sql = "
            SELECT 
			EXTRACT(HOUR FROM submitted) as `hour`,
            count(*) as `count`
            FROM task 
            GROUP by EXTRACT(HOUR FROM submitted)
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute();

        return $statement->fetchAll();
    }

    /**
     * Vložení úloh do db
     *
     * @param array $data
     */
    public function insertTasks(array $data) : void
    {
        $this->insertRows($data, [self::COL_NAME, self::COL_STUDENT_ID, self::COL_SUBMITTED, self::COL_RESULT]);
    }

    public function removeAllTasks() : void
    {
        $statement = $this->getConnection()->prepare("DELETE FROM task;");
        $statement->execute();
    }

    /**
     * Vrací celkový počet úloh
     *
     * @return int
     */
    public function getTotalTaskCount() : int
    {
        $tasks = $this->findAll();

        return count($tasks);
    }

    /**
     * Init tabulky
     */
    public function createTable() : void
    {
        $sql = "
            CREATE TABLE `task` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `name` varchar(11) DEFAULT NULL,
              `student_id` int(10) unsigned DEFAULT NULL,
              `submitted` datetime DEFAULT NULL,
              `result` tinyint(1) DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `ibfk_1_student` (`student_id`),
              CONSTRAINT `ibfk_1_student` FOREIGN KEY (`student_id`) REFERENCES `student` (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute();
    }

    /**
     * Zsíkání dat pro celkový přehled
     *
     * @return array
     */
    public function getOverviewData() : array
    {
        $sql = "
        SELECT 
            t.name as name, 
            COUNT(*) as total,
            SUM(CASE WHEN t.result = 1 THEN 1 END) AS is_ok_attempts,
            SUM(CASE WHEN t.result = 0 THEN 1 END) AS is_not_ok_attempts,
            (SUM(CASE WHEN t.result = 1 THEN 1 END) / COUNT(*)) as succ_rate,
            students_succ_try.`count` as students_success_count,
            students_try.`count` as student_try_count,
            (COUNT(*) / students_try.`count`) as avg_per_student
        FROM task t
        LEFT JOIN (
            SELECT COUNT(DISTINCT s.id) as `count`, t2.name as task_name FROM student s LEFT JOIN task t2 on s.id = t2.student_id  AND t2.result = 1 GROUP BY `name`
            ) as students_succ_try ON students_succ_try.task_name = t.name
        LEFT JOIN (
            SELECT COUNT(DISTINCT s.id) as `count`, t2.name as task_name FROM student s LEFT JOIN task t2 on s.id = t2.student_id  GROUP BY `name`
            ) as students_try ON students_try.task_name = t.name
        
        GROUP BY t.name
        ";

        $statement = $this->getConnection()->query($sql);

        return $statement->fetchAll();
    }

    /**
     * Zsíkání maximálního počtu pokusů pro odevzdání úlohy u úkolů
     *
     * @return array
     */
    public function getOverviewMaxStudentAttemptsPerTask() : array
    {
        $sql = "
            SELECT t.name, max_attempts.count
            FROM task t 
            LEFT JOIN (SELECT COUNT(t.id) as `count`, t.`name` as name FROM task t group by `name`, student_id ORDER by COUNT(t.id) ASC ) as max_attempts on max_attempts.name = t.name
            GROUP BY name;
        ";

        $statement = $this->getConnection()->query($sql);

        return $statement->fetchAll(\PDO::FETCH_KEY_PAIR);
    }

    /**
     * Získání dat pro celkový přehled (součty)
     *
     * @return array
     */
    public function getOverviewDataTotal() : array
    {
        $sql = "
        SELECT 
            t.name as name, 
            COUNT(*) as total,
            SUM(CASE WHEN t.result = 1 THEN 1 END) AS is_ok_attempts,
            SUM(CASE WHEN t.result = 0 THEN 1 END) AS is_not_ok_attempts,
            (SUM(CASE WHEN t.result = 1 THEN 1 END) / COUNT(*)) as succ_rate,
            students_try.`count` as student_try_count
        FROM task t
        LEFT JOIN (
            SELECT COUNT(DISTINCT s.id) as `count`, t2.name as task_name FROM student s LEFT JOIN task t2 on s.id = t2.student_id  GROUP BY `name`
            ) as students_try ON students_try.task_name = t.name
        ";

        $statement = $this->getConnection()->query($sql);

        return $statement->fetch();
    }

    public function getMaxSubmittedDate() : string
    {
        $sql = "
            SELECT MAX(submitted) FROM task;
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute();

        return $statement->fetchColumn();
    }



}