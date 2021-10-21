<?php

namespace Controllers;
use Models\DataModel;
use View\DefaultView;

class BaseController
{
    public function __construct()
    {
        $this->dataModel  = new DataModel();
        $this->defaultView = new DefaultView();
    }

    protected function checkPost()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            return true;
        }
        return false;
    }

    public function location($url)
    {
        header("Location: $url");
    }

    public function homeLocation()
    {
        header('Location: /');
    }
}
