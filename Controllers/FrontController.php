<?php

declare(strict_types=1);

namespace Controllers;

/**
 * Main class for routing
 * @package Controllers
 */
class FrontController
{
    /**
     * Given controller
     * @var string
     */
    protected string $controller;

    /**
     * Given action
     * @var string
     */
    protected string $action;

    /**
     * Given params values
     * @var array|null
     */
    protected ?array $params = null;

    /**
     * Request uri
     * @var string
     */
    protected string $request;

    public function __construct()
    {
        $this->request = $_SERVER['REQUEST_URI'];
    }

    /**
     * Definition and selecting controller and action
     * @return void
     */
    public function start(): void
    {
        $splits = explode('/', trim($this->request, '/'));
        if ($splits[0] == 'admin') {
            $this->controller = !empty($splits[1]) ?
                $this->getFullControllerName('Admin\\' . ucfirst($splits[1]) . 'Controller')
                : $this->getFullControllerName('Admin\\IndexController');
            $this->action = !empty($splits[2]) ? $splits[2] . 'Action' : 'indexAction';
            if (!empty($splits[3])) {
                for ($i = 3, $count = count($splits); $i < $count; $i++) {
                    $this->params[] = $splits[$i];
                }
            }
        } else {
            $this->controller = !empty($splits[0]) ?
                $this->getFullControllerName(ucfirst($splits[0]) . 'Controller')
                : $this->getFullControllerName('IndexController');
            $this->action = !empty($splits[1]) ? $splits[1] . 'Action' : 'indexAction';
            if (!empty($splits[2])) {
                for ($i = 2, $count = count($splits); $i < $count; $i++) {
                    $this->params[] = $splits[$i];
                }
            }
        }
    }

    /**
     * Choose the correct controller and action using reflectionClass
     * @return void
     * @throws \ReflectionException
     */
    public function route(): void
    {
        $this->start();
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

    /**
     * Return correct name for including file with class
     * @param string $name
     * @return string
     */
    private function getFullControllerName(string $name): string
    {
        return '\Controllers\\' . $name;
    }

    /**
     * Set http response code 404, and include 404 not found page
     * @return void
     */
    public function ErrorPage404(): void
    {
        http_response_code(404);
        include_once('Templates/layouts/404.phtml');
        die();
    }
}
