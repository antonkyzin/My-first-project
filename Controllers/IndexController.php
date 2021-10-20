<?php

namespace Controllers;

use Models;
use View\DefaultView;

class IndexController
{
    private $defaultView;

    public function __construct()
    {
        $this->defaultView = new DefaultView();
    }

    public function indexAction()
    {
        $this->defaultView->render();
    }
}
