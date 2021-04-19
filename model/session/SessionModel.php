<?php


namespace App\Model\Session;


class SessionModel
{
    const TEACHER_REGISTRATION_ERROR = "teacher_registration_error";

    const LOGIN_ERROR = "login_error";

    const USER_ROLE = "user_role";

    const IS_LOGGED = "is_logged";

    public function loginUser(string $role) : void
    {
        $this->setUserRole($role);
        $this->setIsLogged(true);
    }

    public function logOutUser() : void
    {
        $this->setUserRole(NULL);
        $this->setIsLogged(false);
    }

    public function getUserRole() : ?string
    {
        return $_SESSION[self::USER_ROLE] ?? NULL;
    }

    public function setUserRole(?string $userRole) : void
    {
        $_SESSION[self::USER_ROLE] = $userRole;
    }

    public function setShowTeacherRegistrationError(bool $state) : void
    {
        $_SESSION[self::TEACHER_REGISTRATION_ERROR] = $state;
    }

    public function setIsLogged(bool $isLogged) : void
    {
        $_SESSION[self::IS_LOGGED] = (int)$isLogged;
    }

    public function isLogged() : bool
    {
        return (bool)$_SESSION[self::IS_LOGGED] ?? false;
    }

    public function isSetShowTeacherRegistrationError() : bool
    {
        if (isset($_SESSION[self::TEACHER_REGISTRATION_ERROR]))
        {
            return $_SESSION[self::TEACHER_REGISTRATION_ERROR];
        }

        return false;
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
        $this->setShowLoginError(false);
        $this->setShowTeacherRegistrationError(false);
    }
}