<?php


namespace App\Model\Repository;


class SettingsRepository extends Repository
{
    const TABLE_NAME = 'settings';

    protected string $tableName = self::TABLE_NAME;

    const COL_KEY = 'key';
    const COL_VALUE = 'value';

    const TEACHER_PASSWORD_KEY = 'teacher_password';

    public function getTeacherPassword() : ?string
    {
        $row = $this->findBy([self::COL_KEY => self::TEACHER_PASSWORD_KEY]);

        return $row[0][self::COL_VALUE];
    }

    public function updateTeacherPassword(string $newPassword) : void
    {
        $statement = $this->pdo->prepare('UPDATE settings SET `value` = ? WHERE `key` = "teacher_password"');

        $statement->execute([$newPassword]);
    }
}