<?php


namespace App\Controller;


use App\System\Response;

class SummaryController extends BaseController
{


    public function actionDefault() : Response
    {

        $data = [
            "tab" => "celkove",
        ];

        return parent::render("summary/default.twig", $data);
    }
}