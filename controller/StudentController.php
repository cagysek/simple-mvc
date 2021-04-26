<?php


namespace App\Controller;


use App\enum\EMenuTab;
use App\Enum\EStatusCode;
use App\Enum\EUserRole;
use App\Model\Facade\SummaryFacade;
use App\Model\Repository\StudentRepository;
use App\Model\Repository\TaskRepository;
use App\System\Request;
use App\System\Response;

class StudentController extends BaseController
{

    private StudentRepository $studentRepository;

    private TaskRepository $taskRepository;

    public function __construct()
    {
        parent::__construct();

        $this->studentRepository = new StudentRepository();
        $this->taskRepository = new TaskRepository();


    }

    public function actionList() : Response
    {
        // aktuální maximální počet odevzdaných úloh
        $currentTaskCount = $this->taskRepository->getMaxTaskNumber();

        $studentsListData = $this->studentRepository->getStudentListData($currentTaskCount);

        $data = [
            "tab" => EMenuTab::STUDENTS,
            "data" => $studentsListData,
            "taskCount" => $currentTaskCount,
            "studentCount" => $this->studentRepository->getTotalStudentsCount(),
        ];

        return parent::render("template/student/list.twig", $data);
    }


    public function actionSelectStudent(Request $request) : Response
    {
        $body = $request->getBody();

        $schoolNumber = $body['OBSLUHA_studenti_zobrazStudenta'];

        if ($this->sessionModel->getUserRole() != EUserRole::TEACHER)
        {
            return new Response(EStatusCode::REDIRECT, NULL, '/public/navod');
        }

        $this->sessionModel->setStudentSchoolNumber($schoolNumber);

        return new Response(EStatusCode::REDIRECT, NULL, '/public/osobniCislo');
    }
}