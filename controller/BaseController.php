<?php


namespace App\Controller;


use App\Enum\EStatusCode;
use App\Model\StudentRepository;
use App\Service\TwigService;
use App\System\Response;

class BaseController
{

    private StudentRepository $studentRepository;

    /**
     * BaseController constructor.
     */
    public function __construct()
    {
        $this->studentRepository = new StudentRepository();
    }

    public function render(string $file, array $params = []) : Response
    {
        $twig = TwigService::getInstance();

        $commonParams = [
            'students_count' => $this->studentRepository->getTotalStudentsCount(),
        ];


        $data = array_merge($params,$commonParams);


        return new Response(EStatusCode::SUCCESS, $twig->render($file, $data));
    }
}