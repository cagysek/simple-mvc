<?php


namespace App\Controller;


use App\enum\EMenuTab;
use App\Enum\EStatusCode;
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

    /**
     * Akce pro zobrazení celkového přehledu
     *
     * @return Response
     */
    public function actionDefault() : Response
    {
        $tableData = $this->summaryFacade->getOverviewData();

        $totalStudentCount = $this->studentRepository->getTotalStudentsCount();

        $data = [
            "tab" => EMenuTab::OVERALL,
            "taskCount" => $this->taskRepository->getMaxTaskNumber(),
            "taskData" => $tableData['task'] ?? [],
            "totalData" => $tableData['total'] ?? [],
            "totalStudentCount" => $totalStudentCount,
            "graphData" => json_encode($this->summaryFacade->getGraphDataForOverview(), JSON_FORCE_OBJECT),
            "validToDate" => $this->taskRepository->getMaxSubmittedDate(),
        ];

        return parent::render("template/summary/default.twig", $data);
    }

    /**
     * Akce pro zobrazení statistik studenta
     *
     * @return Response
     */
    public function actionStudentInfo() : Response
    {

        $studentNumber = $this->sessionModel->getStudentSchoolNumber();

        $student = $this->studentRepository->getStudentBySchoolNumber($studentNumber);
        $studentId = $student[StudentRepository::COL_ID];

        $studentTableData = $this->summaryFacade->getStudentTableData($studentId);

        $data = [
            "tab" => EMenuTab::STUDENT,
            "studentInfo" => $student,
            "studentData" => $studentTableData['student'] ?? [],
            "studentTotalData" => $studentTableData['total'] ?? [],
            "taskCount" => $this->taskRepository->getMaxTaskNumber(),
            "progressBarData" => $this->summaryFacade->getProgressBarData($studentId),
            "graphData" => json_encode($this->summaryFacade->getGraphDataForStudent($studentId), JSON_FORCE_OBJECT),
        ];


        return parent::render("template/summary/studentInfo.twig", $data);
    }
}