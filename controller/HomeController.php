<?php


namespace App\Controller;


use App\enum\EMenuTab;
use App\Service\DatabaseService;
use App\System\Response;

class HomeController extends BaseController
{


    /**
     * HomeController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function actionDefault($a) : Response
    {

        $data = [
            "tab" => EMenuTab::HELP,
        ];

        return parent::render("template/home/default.twig", $data);
    }

    public function actionDetail()
    {
        echo "detail";
    }

}