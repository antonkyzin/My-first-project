<?php

namespace Controllers;

use Models;

class ViewController
{
    private $vievMmodel;

    public function __construct()
    {
        $this->vievMmodel = new Models\ViewModel();
    }

    public function indexAction()
    {
        $this->vievMmodel->render();
    }
}
