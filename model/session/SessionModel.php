<?php


namespace App\Model\Session;


class SessionModel
{
    const ERROR = "error";

    const USER_ROLE = "user_role";

    const IS_LOGGED = "is_logged";

    const STUDENT_SCHOOL_NUMBER = "student_school_number";

    public function loginUser(string $role) : void
    {
        $this->setUserRole($role);
        $this->setIsLogged(true);
    }

    public function setStudentSchoolNumber(?string $studentSchoolNumber) : void
    {
        $_SESSION[self::STUDENT_SCHOOL_NUMBER] = $studentSchoolNumber;
    }

    public function getStudentSchoolNumber() : ?string
    {
        return $_SESSION[self::STUDENT_SCHOOL_NUMBER] ?? NULL;
    }

    public function logOutUser() : void
    {
        $this->setUserRole(NULL);
        $this->setIsLogged(false);
        $this->setStudentSchoolNumber(NULL);
    }

    public function getUserRole() : ?string
    {
        return $_SESSION[self::USER_ROLE] ?? NULL;
    }

    public function setUserRole(?string $userRole) : void
    {
        $_SESSION[self::USER_ROLE] = $userRole;
    }

    public function setError(?string $text) : void
    {
        $_SESSION[self::ERROR] = $text;
    }

    public function getError() : ?string
    {
        if (isset($_SESSION[self::ERROR]))
        {
            return $_SESSION[self::ERROR];
        }

        return NULL;
    }

    public function setIsLogged(bool $isLogged) : void
    {
        $_SESSION[self::IS_LOGGED] = (int)$isLogged;
    }

    public function isLogged() : bool
    {
        return (bool)$_SESSION[self::IS_LOGGED] ?? false;
    }


    public function setShowLoginError(bool $state) : void
    {
        $_SESSION[self::LOGIN_ERROR] = $state;
    }

    public function isSetShowLoginError() : bool
    {
        return $_SESSION[self::LOGIN_ERROR] ?? false;
    }


    public function clearTmpData()
    {
        $this->setError(NULL);
    }
}