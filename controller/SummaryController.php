<?php


namespace App\Controller;


use App\Model\Repository\StudentRepository;
use App\System\Response;

class SummaryController extends BaseController
{

    private StudentRepository $studentRepository;

    public function __construct()
    {
        $this->studentRepository = new StudentRepository();

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

        $data = [
            "tab" => "osobniCislo",
            "studentInfo" => $student,
        ];

        return parent::render("template/summary/default.twig", $data);
    }
}