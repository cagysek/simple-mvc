<?php


namespace App\Controller;


use App\enum\EMenuTab;
use App\Enum\EStatusCode;
use App\Enum\EUserRole;
use App\Model\Repository\StudentRepository;
use App\System\Request;
use App\System\Response;


class SettingsController extends BaseController
{

    private StudentRepository $studentRepository;

    /**
     * SettingsController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->checkPrivileges();

        $this->studentRepository = new StudentRepository();
    }

    private function checkPrivileges()
    {
        if ($this->sessionModel->getUserRole() != EUserRole::TEACHER)
        {
            header("Location: /public/");
        }
    }

    public function actionDefault() : Response
    {




        $data = [
            "tab" => EMenuTab::SETTINGS,
            "studentsWithPassword" => $this->studentRepository->getStudentsWithPassword(),
        ];

        return parent::render("template/settings/default.twig", $data);
    }

    public function actionResetDatabase() : Response
    {

    }

    public function actionResetStudentPassword(Request $request) : Response
    {
        $body = $request->getBody();

        $studentSchoolNumber = $body['resetOsCislo'];

        $this->studentRepository->resetStudentPassword($studentSchoolNumber);

        $this->sessionModel->setSuccessMessage("Reset hesla studenta proběhl.");

        return new Response(EStatusCode::REDIRECT, "", "/public/nastaveni");
    }

    public function actionUpdateTasks() : Response
    {

    }

    public function actionInitialization() : Response
    {

    }

}