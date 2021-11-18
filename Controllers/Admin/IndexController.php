<?php
declare(strict_types=1);

namespace Controllers\Admin;

use View\DefaultView;

/**
 * @package Controllers\Admin
 */
class IndexController
{
    /**
     * @var DefaultView
     */
    private $defaultView;

    public function __construct()
    {
        $this->defaultView = new DefaultView();
    }

    /**
     * Render admin home page
     * @return void
     */
    public function indexAction(): void
    {
        $options['content'] = isset($_SESSION['user']['access']) ? 'admin/admin_main.phtml' : 'main.phtml';
        $this->defaultView->render($options);
    }
}
