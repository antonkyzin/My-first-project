<?php

namespace Controllers;

class BaseController
{
    protected function checkPost()
    {
        return $_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST);
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
