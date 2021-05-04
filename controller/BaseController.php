<?php

/**
 * Rodičovská třída pro kontrolery
 */


namespace App\Controller;


use App\Enum\EStatusCode;
use App\Enum\EUserRole;
use App\model\facade\SettingsFacade;
use App\Model\Repository\SettingsRepository;
use App\Model\Repository\StudentRepository;
use App\Model\Session\SessionModel;
use App\Service\TwigService;
use App\System\Request;
use App\System\Response;

class BaseController
{

    private StudentRepository $studentRepository;

    protected SessionModel $sessionModel;

    private SettingsRepository $settingsRepository;

    private SettingsFacade $settingsFacade;

    protected array $environmentParameters;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->studentRepository = new StudentRepository();
        $this->sessionModel = new SessionModel();
        $this->settingsRepository = new SettingsRepository();
        $this->settingsFacade = new SettingsFacade();

        $this->settingsFacade->checkDatabase();

        $this->environmentParameters = include(__DIR__ . '/../config/env.php');

    }

    /**
     * Kontrola práv uživatele
     *
     * @param array $rolesWithAccess pole povolených rolí
     */
    protected function checkPrivileges(array $rolesWithAccess)
    {
        if (!in_array($this->sessionModel->getUserRole(), $rolesWithAccess))
        {
            $this->sessionModel->setErrorMessage("Do této sekce nemáte přístup");

            header("Location: " . $this->environmentParameters['path_to_root'] . '/navod');

            die;
        }
    }

    /**
     * Render metoda, obsahující základní prvky, každá akce by měla volat tuto metodu, pokud něco vykresluje
     *
     * @param string $file cesta k šabloně
     * @param array  $params další parametry pro šablony
     * @return Response
     */
    public function render(string $file, array $params = []) : Response
    {
        try
        {
            $twig = TwigService::getInstance();

            $commonParams = [
                'studentsCount' => $this->studentRepository->getTotalStudentsCount(),
                'errorMsg' => $this->sessionModel->getErrorMessage(),
                'successMsg' => $this->sessionModel->getSuccessMessage(),
                'nonRegisteredStudents' => $this->studentRepository->getStudentsSchoolNumbersWithoutPassword(),
                'registeredStudents' => $this->studentRepository->getStudentSchoolNumbersWithPassword(),
                'role' => $this->sessionModel->getUserRole(),
                'selectedStudentNumber' => $this->sessionModel->getStudentSchoolNumber(),
                'baseUri' => $this->environmentParameters['path_to_root'],
            ];

            $data = array_merge($params,$commonParams);

            $this->sessionModel->clearTmpData();

            return new Response(EStatusCode::SUCCESS, $twig->render($file, $data));
        }
        catch (\Throwable $e)
        {
            return new Response(EStatusCode::INTERNAL_ERROR, $e->getMessage());
        }
    }

    /**
     * Akce pro přihlášení studenta
     *
     * @param Request $request
     * @return Response
     */
    public function actionLoginStudent(Request $request) : Response
    {
        $studentSchoolNumber = $request->getBody()['loginOsCisloUci'];
        $password = $request->getBody()['loginHesloStu'];

        if (empty($password))
        {
            return new Response(EStatusCode::REDIRECT, "", "/navod");
        }

        if ($studentSchoolNumber == "--- Nevybráno ---")
        {
            $this->sessionModel->setErrorMessage("Nebylo zvoleno žádné osobní číslo.");

            return new Response(EStatusCode::REDIRECT, "", "/navod");

        }

        $studentPassword = $this->studentRepository->getStudentPassword($studentSchoolNumber);

        if (password_verify($password, $studentPassword))
        {
            $this->sessionModel->loginUser(EUserRole::STUDENT);
            $this->sessionModel->setStudentSchoolNumber($studentSchoolNumber);

        }
        else
        {
            $this->sessionModel->setErrorMessage("Přihlášení se nezdařilo. Zadané heslo není správné.");
        }

        return new Response(EStatusCode::REDIRECT, "", "/navod");
    }

    /**
     * Akce pro přihlášení studenta
     *
     * @param Request $request
     * @return Response
     */
    public function actionLoginTeacher(Request $request) : Response
    {
        $password = $request->getBody()['loginHesloUci'];

        if (empty($password))
        {
            return new Response(EStatusCode::REDIRECT, "", "/navod");
        }

        $teacherPassword = $this->settingsRepository->getTeacherPassword();

        if (password_verify($password, $teacherPassword))
        {
            $this->sessionModel->loginUser(EUserRole::TEACHER);

            $defaultStudent = $this->studentRepository->getStudentById(1);

            if ($defaultStudent)
            {
                $this->sessionModel->setStudentSchoolNumber($defaultStudent[StudentRepository::COL_SCHOOL_NUMBER]);
            }
        }
        else
        {
            $this->sessionModel->setErrorMessage("Přihlášení se nezdařilo. Zadané heslo není správné.");
        }

        return new Response(EStatusCode::REDIRECT, "", "/navod");
    }

    /**
     * Akce pro registraci studenta
     *
     * @param Request $request
     * @return Response
     */
    public function actionRegisterStudent(Request $request) : Response
    {
        $schoolNumber = $request->getBody()['regOsCisloUci'];
        $password = $request->getBody()['regHesloStu'];

        if (empty($schoolNumber) || $schoolNumber == "--- Nevybráno ---")
        {
            $this->sessionModel->setErrorMessage("Nebylo zvoleno žádné osobní číslo.");

            return new Response(EStatusCode::REDIRECT, "", "/navod");
        }

        if (empty($password))
        {
            $this->sessionModel->setErrorMessage("Nebylo zvoleno žádné heslo.");

            return new Response(EStatusCode::REDIRECT, "", "/navod");
        }

        $newPasswordHash = password_hash($password, PASSWORD_BCRYPT);

        $this->studentRepository->updateStudentPassword($schoolNumber, $newPasswordHash);

        $this->sessionModel->loginUser(EUserRole::STUDENT);
        $this->sessionModel->setStudentSchoolNumber($schoolNumber);

        return new Response(EStatusCode::REDIRECT, "", "/navod");
    }

    /**
     * Akce pro registraci učitele
     *
     * @param Request $request
     * @return Response
     */
    public function actionRegisterTeacher(Request $request) : Response
    {
        $password = $request->getBody()["regHesloUci"];

        if (empty($password))
        {
            return new Response(EStatusCode::REDIRECT, "", "/navod");
        }

        $teacherPassword = $this->settingsRepository->getTeacherPassword();

        if (empty($teacherPassword))
        {
            $newPasswordHash = password_hash($password, PASSWORD_BCRYPT);

            $this->settingsRepository->updateTeacherPassword($newPasswordHash);

            $this->sessionModel->loginUser(EUserRole::TEACHER);
        }
        else
        {
            $this->sessionModel->setErrorMessage("Registrace se nezdařila. Byla již provedena dříve.");
        }

        return new Response(EStatusCode::REDIRECT, "", "/navod");
    }

    /**
     * Akce pro odhlášení
     *
     * @return Response
     */
    public function actionLogout() : Response
    {
        $this->sessionModel->logOutUser();

        return new Response(EStatusCode::REDIRECT, "", "/navod");
    }

    /**
     * Akce pro změnu hesla
     *
     * @param Request $request
     * @return Response
     */
    public function actionChangePassword(Request $request) : Response
    {
        $params = $request->getBody();

        if ($this->sessionModel->getUserRole() == EUserRole::TEACHER)
        {
            $newPassword = $params['zmenaHesloUci'];

            $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

            $this->settingsRepository->updateTeacherPassword($newPasswordHash);
        }
        else
        {
            $newPassword = $params['zmenaHesloStu'];

            $newPasswordHash = password_hash($newPassword, PASSWORD_BCRYPT);

            $schoolNumber = $this->sessionModel->getStudentSchoolNumber();

            $this->studentRepository->updateStudentPassword($schoolNumber, $newPasswordHash);
        }

        $this->sessionModel->setSuccessMessage("Změna hesla proběhla.");

        return new Response(EStatusCode::REDIRECT, "", $request->getReferer());
    }
}