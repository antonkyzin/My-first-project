<?php

namespace Controllers;

use Models;

class ViewController implements IController
{
    private $fc;
    private $model;

    public function __construct()
    {
        $this->fc = FrontController::getInstance();
        $this->model = new Models\ViewModel();
    }

    public function indexAction()
    {
        $output = $this->model->renderIndex();
        $this->fc->setBody($output);
    }

    public function renderAction()
    {
        $params = $this->fc->getParams();
        $output = $this->model->render($params["opt"]);
        $this->fc->setBody($output);
  }
}
