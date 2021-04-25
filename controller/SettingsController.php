<?php


namespace App\Controller;


use App\enum\EMenuTab;
use App\Enum\EStatusCode;
use App\Enum\EUserRole;
use App\model\facade\SettingsFacade;
use App\Model\Repository\StudentRepository;
use App\Model\Repository\TaskRepository;
use App\System\Request;
use App\System\Response;


class SettingsController extends BaseController
{

    private StudentRepository $studentRepository;
    private SettingsFacade $settingsFacade;
    private TaskRepository $taskRepository;

    /**
     * SettingsController constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->checkPrivileges();

        $this->studentRepository = new StudentRepository();

        $this->settingsFacade = new SettingsFacade();

        $this->taskRepository = new TaskRepository();
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
            "studentsCount" => $this->studentRepository->getTotalStudentsCount(),
            "inputFiles" => $this->settingsFacade->getInputFiles(),
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

    public function actionUpdateTasks(Request $request) : Response
    {
        $body = $request->getBody();

        $file = $body['seznamUloh'];

        $result = $this->settingsFacade->loadTasks($file);

        if ($result)
        {
            $this->sessionModel->setSuccessMessage("Soubor byl načten. <br> Počet záznamů, které byly uloženy do DB: " . $this->taskRepository->getTotalTaskCount());
        }
        else
        {
            $this->sessionModel->setErrorMessage("Soubor neobsahuje data s úlohami.");
        }

        return new Response(EStatusCode::REDIRECT, "", "/public/nastaveni");

    }

    public function actionInitialization(Request $request) : Response
    {
        $body = $request->getBody();

        $file = $body['seznamStudentu'];
        $taskCount = $body['inicPocetUloh'];

        $result = $this->settingsFacade->loadStudents($file);

        $this->settingsFacade->updateTotalTaskCount($taskCount);


        if ($result)
        {
            $this->sessionModel->setSuccessMessage("Soubor byl načten. <br> Počet záznamů, které byly uloženy do DB: " . $this->studentRepository->getTotalStudentsCount());
        }
        else
        {
            $this->sessionModel->setErrorMessage("Soubor neobsahuje data se studenty.");
        }

        return new Response(EStatusCode::REDIRECT, "", "/public/nastaveni");
    }

}