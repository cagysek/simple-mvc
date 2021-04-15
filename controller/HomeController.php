<?php


namespace App\Controller;


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

//        foreach ($result as $row)
//        {
//            echo $row['os_cislo'] . '</br>';
//        }
//
//        echo "defaulttt";
//        echo "$a";

        $data = [
            "tab" => "navod",
        ];

        return parent::render("home/default.twig", $data);
    }

    public function actionDetail()
    {
        echo "detail";
    }

}