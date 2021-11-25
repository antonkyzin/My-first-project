<?php
declare(strict_types=1);

namespace Controllers\Admin;

use Models\DataRegistry;
use Interfaces\IDataManagement;
use View\DefaultView;

/**
 * @package Controllers\Admin
 */
class IndexController
{
    private DefaultView $defaultView;

    /**
     * Object for access to session data
     */
    private IDataManagement $sessionData;

    public function __construct()
    {
        $this->defaultView = new DefaultView();
        $this->sessionData = DataRegistry::getInstance()->get('session');
    }

    /**
     * Render admin home page
     *
     * @return void
     */
    public function indexAction(): void
    {
        $content = ($this->sessionData->getUser() !== null) ? 'admin/admin_main.phtml' : 'main.phtml';
        $options = $this->defaultView->getOptions('Главная', $content);
        $this->defaultView->render($options);
    }
}
