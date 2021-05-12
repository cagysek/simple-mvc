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

        // povolení pouze orle učitele pro všechny akce
        $this->checkPrivileges([EUserRole::TEACHER]);

        $this->studentRepository = new StudentRepository();

        $this->settingsFacade = new SettingsFacade();

        $this->taskRepository = new TaskRepository();
    }

    /**
     * Akce pro zobrazení stránky s nastavením
     * @return Response
     */
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

    /**
     * Obsluha resetu databáze
     *
     * @return Response
     */
    public function actionResetDatabase() : Response
    {
        $this->settingsFacade->initDatabase();
        $this->sessionModel->logOutUser();

        return new Response(EStatusCode::REDIRECT, "", "/navod");
    }

    /**
     * Obsluha resetu hesla studenta
     *
     * @param Request $request
     * @return Response
     */
    public function actionResetStudentPassword(Request $request) : Response
    {
        $body = $request->getBody();

        $studentSchoolNumber = $body['resetOsCislo'];

        $this->studentRepository->resetStudentPassword($studentSchoolNumber);

        $this->sessionModel->setSuccessMessage("Reset hesla studenta proběhl.");

        return new Response(EStatusCode::REDIRECT, "", "/nastaveni");
    }

    /**
     * Obsluha načtení úloh
     *
     * @param Request $request
     * @return Response
     */
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
            $this->sessionModel->setErrorMessage("Soubor se nepodařilo načíst, zkontrolujete, že obsahuje validní data.");
        }

        return new Response(EStatusCode::REDIRECT, "", "/nastaveni");

    }

    /**
     * Obsluha načtení souboru se studenty a nastavením počtu úloh
     *
     * @param Request $request
     * @return Response
     */
    public function actionInitialization(Request $request) : Response
    {
        $body = $request->getBody();

        $file = $body['seznamStudentu'];
        $taskCount = $body['inicPocetUloh'];

        $result = $this->settingsFacade->loadStudents($file);

        $this->settingsFacade->updateTotalTaskCount($taskCount);

        if ($result)
        {
            // nastaví defaultního uživatele do menu
            $defaultStudent = $this->studentRepository->getStudentById(1);

            if ($defaultStudent)
            {
                $this->sessionModel->setStudentSchoolNumber($defaultStudent[StudentRepository::COL_SCHOOL_NUMBER]);
            }

            $this->sessionModel->setSuccessMessage("Soubor byl načten. <br> Počet záznamů, které byly uloženy do DB: " . $this->studentRepository->getTotalStudentsCount());
        }
        else
        {
            $this->sessionModel->setErrorMessage("Soubor neobsahuje data se studenty.");
        }

        return new Response(EStatusCode::REDIRECT, "", "/nastaveni");
    }

}