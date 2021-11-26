<?php
declare(strict_types=1);

namespace Controllers;

use Interfaces\IDataManagement;
use Models\DataRegistry;
use Models\Logger;
use Models\Server;
use Models\Session;
use Models\Post;
use Models\File;
use Models\Config;

/**
 * Main class for routing
 *
 * @package Controllers
 */
class FrontController
{
    /**
     * Given controller
     */
    protected string $controller;

    /**
     * Given action
     */
    protected string $action;

    /**
     * Given params values
     */
    protected ?array $params = null;

    /**
     * Request uri
     */
    protected string $request;

    /**
     * Object for access to server data
     */
    private IDataManagement $serverData;

    public Logger $logger;


    public function __construct()
    {
        $this->logger = new Logger();
        try {
            $this->registerData();
            $this->serverData = DataRegistry::getInstance()->get('server');
        } catch (\Exception $exception) {
            $this->logger->log($exception->getMessage() . $exception->getTraceAsString());
        }
    }

    /**
     * Register server and session models for encapsulating access
     *
     * @return void
     * @throws \Exception
     */
    private function registerData(): void
    {
        $register = DataRegistry::getInstance();
        $register->register('server', new Server\Manager())
            ->register('session', new Session\Manager())
            ->register('post', new Post\Manager())
            ->register('file', new File\Manager())
            ->register('config', new Config\Manager);
    }

    /**
     * @return string
     */
    public function getRequest(): string
    {
        return $this->serverData->getRequestUri();
    }

    /**
     * Definition and selecting controller and action
     *
     * @return void
     */
    public function start(): void
    {
        $this->request = $this->getRequest();
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
     *
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
     *
     * @param string $name
     * @return string
     */
    private function getFullControllerName(string $name): string
    {
        return '\Controllers\\' . $name;
    }

    /**
     * Set http response code 404, and include 404 not found page
     *
     * @return void
     */
    public function ErrorPage404(): void
    {
        http_response_code(404);
        include_once('Templates/layouts/404.phtml');
        die();
    }
}
