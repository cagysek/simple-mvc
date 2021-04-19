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
            'students_count' => $this->studentRepository->getTotalStudentsCount(),
            'isSetShowTeacherRegistrationError' => $this->sessionModel->isSetShowTeacherRegistrationError(),
            'test' => $this->settingsRepository->getTeacherPassword(),
            'role' => $this->sessionModel->getUserRole(),
        ];


        $data = array_merge($params,$commonParams);

        $this->sessionModel->clearTmpData();

        return new Response(EStatusCode::SUCCESS, $twig->render($file, $data));
    }

    public function actionLoginStudent(Request $request) : Response
    {

        //$_SESSION["test"] = $request->getBody()["regHesloUci"];

        $this->sessionModel->setShowTeacherRegistrationError(true);

        return new Response(EStatusCode::REDIRECT, "", "/public/navod");
    }

    public function actionLoginTeacher(Request $request) : Response
    {

        //$_SESSION["test"] = $request->getBody()["regHesloUci"];

        $this->sessionModel->setShowTeacherRegistrationError(true);

        return new Response(EStatusCode::REDIRECT, "", "/public/navod");
    }

    public function actionRegisterStudent(Request $request) : Response
    {

        //$_SESSION["test"] = $request->getBody()["regHesloUci"];

        $this->sessionModel->setShowTeacherRegistrationError(true);

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
            $newPasswordHash = password_hash($teacherPassword, PASSWORD_BCRYPT);

            $this->settingsRepository->updateTeacherPassword($newPasswordHash);

            $this->sessionModel->loginUser(EUserRole::TEACHER);
        }
        else
        {
            $this->sessionModel->setShowTeacherRegistrationError(true);
        }

        return new Response(EStatusCode::REDIRECT, "", "/public/navod");
    }

    public function actionLogOut() : Response
    {
        $this->sessionModel->logOutUser();

        return new Response(EStatusCode::REDIRECT, "", "/public/navod");
    }
}