<?php


namespace App\Controller;


use App\Enum\EStatusCode;
use App\Enum\EUserRole;
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

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->studentRepository = new StudentRepository();
        $this->sessionModel = new SessionModel();
        $this->settingsRepository = new SettingsRepository();
    }

    public function render(string $file, array $params = []) : Response
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
        ];

        $data = array_merge($params,$commonParams);

        $this->sessionModel->clearTmpData();

        return new Response(EStatusCode::SUCCESS, $twig->render($file, $data));
    }

    public function actionLoginStudent(Request $request) : Response
    {
        $studentSchoolNumber = $request->getBody()['loginOsCisloUci'];
        $password = $request->getBody()['loginHesloStu'];

        if (empty($password))
        {
            return new Response(EStatusCode::REDIRECT, "", "/public/navod");
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

        return new Response(EStatusCode::REDIRECT, "", "/public/navod");
    }

    public function actionLoginTeacher(Request $request) : Response
    {
        $password = $request->getBody()['loginHesloUci'];

        if (empty($password))
        {
            return new Response(EStatusCode::REDIRECT, "", "/public/navod");
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

        return new Response(EStatusCode::REDIRECT, "", "/public/navod");
    }

    public function actionRegisterStudent(Request $request) : Response
    {
        $schoolNumber = $request->getBody()['regOsCisloUci'];
        $password = $request->getBody()['regHesloStu'];

        if (empty($schoolNumber) || $schoolNumber == "--- Nevybráno ---")
        {
            $this->sessionModel->setErrorMessage("Nebylo zvoleno žádné osobní číslo.");

            return new Response(EStatusCode::REDIRECT, "", "/public/navod");
        }

        if (empty($password))
        {
            $this->sessionModel->setErrorMessage("Nebylo zvoleno žádné heslo.");

            return new Response(EStatusCode::REDIRECT, "", "/public/navod");
        }

        $newPasswordHash = password_hash($password, PASSWORD_BCRYPT);

        $this->studentRepository->updateStudentPassword($schoolNumber, $newPasswordHash);

        $this->sessionModel->loginUser(EUserRole::STUDENT);
        $this->sessionModel->setStudentSchoolNumber($schoolNumber);

        return new Response(EStatusCode::REDIRECT, "", "/public/navod");
    }

    public function actionRegisterTeacher(Request $request) : Response
    {
        $password = $request->getBody()["regHesloUci"];

        if (empty($password))
        {
            return new Response(EStatusCode::REDIRECT, "", "/public/navod");
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

        return new Response(EStatusCode::REDIRECT, "", "/public/navod");
    }

    public function actionLogout() : Response
    {
        $this->sessionModel->logOutUser();

        return new Response(EStatusCode::REDIRECT, "", "/public/navod");
    }

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