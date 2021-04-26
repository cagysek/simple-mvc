<?php

/**
 * Třída pro obsluhu dat v session
 */

namespace App\Model\Session;


class SessionModel
{
    const ERROR = "error";

    const USER_ROLE = "user_role";

    const IS_LOGGED = "is_logged";

    const STUDENT_SCHOOL_NUMBER = "student_school_number";

    const SUCCESS = "success";

    /**
     * Přihlášení uživatele
     *
     * @param string $role
     */
    public function loginUser(string $role) : void
    {
        $this->setUserRole($role);
        $this->setIsLogged(true);
    }

    /**
     * Nastavení zvoleného školního čísla
     *
     * @param string|null $studentSchoolNumber
     */
    public function setStudentSchoolNumber(?string $studentSchoolNumber) : void
    {
        $_SESSION[self::STUDENT_SCHOOL_NUMBER] = $studentSchoolNumber;
    }

    /**
     * Získání nastaveného školního čísla
     *
     * @return string|null
     */
    public function getStudentSchoolNumber() : ?string
    {
        return $_SESSION[self::STUDENT_SCHOOL_NUMBER] ?? NULL;
    }

    /**
     * Odhlášení uživatele
     */
    public function logOutUser() : void
    {
        $this->setUserRole(NULL);
        $this->setIsLogged(false);
        $this->setStudentSchoolNumber(NULL);
    }

    /**
     * Získání role uživatele
     *
     * @return string|null
     */
    public function getUserRole() : ?string
    {
        return $_SESSION[self::USER_ROLE] ?? NULL;
    }

    /**
     * Nastavení role uživatele
     *
     * @param string|null $userRole
     */
    public function setUserRole(?string $userRole) : void
    {
        $_SESSION[self::USER_ROLE] = $userRole;
    }

    /**
     * Nastavení error zprávy
     *
     * @param string|null $text
     */
    public function setErrorMessage(?string $text) : void
    {
        $_SESSION[self::ERROR] = $text;
    }

    /**
     * Získání error zprávy
     *
     * @return string|null
     */
    public function getErrorMessage() : ?string
    {
        if (isset($_SESSION[self::ERROR]))
        {
            return $_SESSION[self::ERROR];
        }

        return NULL;
    }

    /**
     * Nastvení příznaku, že je uživatel přihlášený
     *
     * @param bool $isLogged
     */
    public function setIsLogged(bool $isLogged) : void
    {
        $_SESSION[self::IS_LOGGED] = (int)$isLogged;
    }

    /**
     * Zsíkání příznaku o přihlášení uživatele
     *
     * @return bool
     */
    public function isLogged() : bool
    {
        return (bool)$_SESSION[self::IS_LOGGED] ?? false;
    }

    /**
     * Nastavení success hlášky
     *
     * @param string|null $state
     */
    public function setSuccessMessage(?string $state) : void
    {
        $_SESSION[self::SUCCESS] = $state;
    }

    /**
     * Získaní success hlášky
     *
     * @return string|null
     */
    public function getSuccessMessage() : ?string
    {
        return $_SESSION[self::SUCCESS] ?? NULL;
    }

    /**
     * Vyčištění dočasných dat
     */
    public function clearTmpData()
    {
        $this->setErrorMessage(NULL);
        $this->setSuccessMessage(NULL);
    }
}