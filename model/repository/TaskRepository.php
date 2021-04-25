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

    public function insertTasks(array $data) : void
    {
        $this->insertRows($data, [self::COL_NAME, self::COL_STUDENT_ID, self::COL_SUBMITTED, self::COL_RESULT]);
    }

    public function getTotalTaskCount() : int
    {
        $tasks = $this->findAll();

        return count($tasks);
    }

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

}