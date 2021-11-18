<?php

declare(strict_types=1);

namespace Controllers;

use View\DefaultView;

/**
 * @package Controllers
 */
class IndexController extends BaseController
{
    /**
     * @var DefaultView
     */
    private DefaultView $defaultView;

    public function __construct()
    {
        $this->defaultView = new DefaultView();
    }

    /**
     * Render default page
     * @return void
     */
    public function indexAction(): void
    {
        $options['content'] = isset($_SESSION['user']) ? 'user_main.phtml' : 'main.phtml';
        $this->defaultView->render($options);
    }
}
