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

    /**
     * Akce pro homepage
     * @return Response
     */
    public function actionDefault() : Response
    {

        $data = [
            "tab" => EMenuTab::HELP,
        ];

        return parent::render("template/home/default.twig", $data);
    }
}