<?php


namespace App\Controller;


use App\Model\Facade\SummaryFacade;
use App\Model\Repository\StudentRepository;
use App\Model\Repository\TaskRepository;
use App\System\Response;

class SummaryController extends BaseController
{

    private StudentRepository $studentRepository;

    private SummaryFacade $summaryFacade;

    private TaskRepository $taskRepository;

    public function __construct()
    {
        $this->studentRepository = new StudentRepository();
        $this->summaryFacade = new SummaryFacade();
        $this->taskRepository = new TaskRepository();

        parent::__construct();
    }

    public function actionDefault() : Response
    {

        $data = [
            "tab" => "celkove",
        ];

        return parent::render("template/summary/default.twig", $data);
    }

    public function actionStudentInfo() : Response
    {

        $studentNumber = $this->sessionModel->getStudentSchoolNumber();

        $student = $this->studentRepository->getStudentBySchoolNumber($studentNumber);
        $studentId = $student[StudentRepository::COL_ID];

        $studentTableData = $this->summaryFacade->getStudentTableData($studentId);


        $data = [
            "tab" => "osobniCislo",
            "studentInfo" => $student,
            "studentData" => $studentTableData['student'],
            "studentTotalData" => $studentTableData['total'],
            "taskCount" => $this->taskRepository->getMaxTaskNumber(),
            "progressBarData" => $this->summaryFacade->getProgressBarData($studentId),
            "graphData" => json_encode($this->summaryFacade->getGraphDataForStudent($studentId), JSON_FORCE_OBJECT),
        ];


        return parent::render("template/summary/studentInfo.twig", $data);
    }
}