<?php

namespace Controllers;

class FrontController
{
    protected $controller;
    protected $action, $params;
    public static $instance;

    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        $request = $_SERVER['REQUEST_URI'];
        $splits = explode('/', trim($request, '/'));
        $this->controller = !empty($splits[0]) ?
            $this->getFullControllerName(ucfirst($splits[0]) . 'Controller')
            : $this->getFullControllerName('IndexController');
        $this->action = !empty($splits[1]) ? $splits[1] . 'Action' : 'indexAction';
        if (!empty($splits[2])) {
            $keys = $values = [];
            for ($i = 2, $count = count($splits); $i < $count; $i++) {
                if ($i % 2 == 0) {
                    $keys[] = $splits[$i];
                } else {
                    $values[] = $splits[$i];
                }
            }
            $this->params = array_combine($keys, $values);
        }
    }

    public function route()
    {
        if (class_exists($this->controller)) {
            $reflector = new \ReflectionClass($this->controller);
            if ($reflector->hasMethod($this->action)) {
                $controller = $reflector->newInstance();
                $method = $reflector->getMethod($this->action);
                $method->invoke($controller, $this->params);
            } else {
                $this->ErrorPage404();
            }
        } else {
            $this->ErrorPage404();
        }
    }

    private function getFullControllerName($name)
    {
        return '\Controllers\\' . $name;
    }

    public function ErrorPage404()
    {
        http_response_code(404);
        include_once('Templates/layouts/404.phtml');
        die();
    }
}
