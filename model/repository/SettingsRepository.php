<?php


namespace App\Model\Repository;


class SettingsRepository extends Repository
{
    const TABLE_NAME = 'settings';

    protected string $tableName = self::TABLE_NAME;

    const COL_KEY = 'key';
    const COL_VALUE = 'value';

    const TEACHER_PASSWORD_KEY = 'teacher_password';
    const TOTAL_TASK_COUNT = 'total_task_count';

    /**
     * Zsíká heslo učitele
     *
     * @return string|null
     */
    public function getTeacherPassword() : ?string
    {
        $row = $this->findBy([self::COL_KEY => self::TEACHER_PASSWORD_KEY]);

        return $row[0][self::COL_VALUE];
    }

    /**
     * Updatuje heslo učitele
     *
     * @param string $newPassword
     */
    public function updateTeacherPassword(string $newPassword) : void
    {
        $statement = $this->getConnection()->prepare('UPDATE settings SET `value` = ? WHERE `key` = "' . self::TEACHER_PASSWORD_KEY . '"');

        $statement->execute([$newPassword]);
    }

    /**
     * Updatuje celkový počet úloh
     *
     * @param int $totalTaskCount
     */
    public function updateTotalTaskCount(int $totalTaskCount) : void
    {
        $statement = $this->getConnection()->prepare('UPDATE settings SET `value` = ? WHERE `key` = "' . self::TOTAL_TASK_COUNT . '"');

        $statement->execute([$totalTaskCount]);
    }

    /**
     * Init tabulky
     */
    public function createTable() : void
    {
        $sql = "
            CREATE TABLE `settings` (
                `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
                `key` varchar(255) DEFAULT NULL,
                `value` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute();

        // vložení defaultních hodnot
        $sql = "
            INSERT INTO `settings` (`key`) VALUES ('teacher_password');
            INSERT INTO `settings` (`key`) VALUES ('total_task_count');
        ";

        $statement = $this->getConnection()->prepare($sql);
        $statement->execute();
    }
}