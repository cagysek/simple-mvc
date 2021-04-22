<?php


namespace App\Controller;


use App\enum\EMenuTab;
use App\System\Response;

class SettingsController extends BaseController
{


    /**
     * SettingsController constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function actionDefault() : Response
    {
        $data = [
            "tab" => EMenuTab::SETTINGS,
        ];

        return parent::render("template/settings/default.twig", $data);
    }

}