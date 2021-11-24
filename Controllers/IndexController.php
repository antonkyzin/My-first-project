<?php
declare(strict_types=1);

namespace Controllers;

use Models\DataRegistry;
use Interfaces\IDataManagement;
use View\DefaultView;

/**
 * @package Controllers
 */
class IndexController extends BaseController
{
    private DefaultView $defaultView;

    /**
     * Object for access to session data
     *
     * @var IDataManagement
     */
    private IDataManagement $sessionData;

    public function __construct()
    {
        $this->defaultView = new DefaultView();
        $this->sessionData = DataRegistry::getInstance()->get('session');
    }

    /**
     * Render default page
     *
     * @return void
     */
    public function indexAction(): void
    {
        $content = $this->sessionData->getUser() !== null ? 'user_main.phtml' : 'main.phtml';
        $options = $this->defaultView->getOptions('Главная', $content);
        $this->defaultView->render($options);
    }
}
