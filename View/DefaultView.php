<?php
declare(strict_types=1);

namespace View;

use Models\DataRegistry;
use Interfaces\IDataManagement;

/**
 * Default class for extends
 *
 * @package View
 */
class DefaultView
{
    /**
     * Object for access to session data
     *
     * @var IDataManagement
     */
    protected IDataManagement $sessionData;

    /**
     * Object for access to server data
     *
     * @var IDataManagement
     */
    protected IDataManagement $serverData;

    public function __construct()
    {
        $this->sessionData = DataRegistry::getInstance()->get('session');
        $this->serverData = DataRegistry::getInstance()->get('server');
    }

    /**
     * Maim method for rendering data
     *
     * @param null|array $options
     * @return void
     */
    public function render(array $options = null): void
    {
//        $options = $this->getOptions($options);
        include_once 'Templates/index.phtml';
    }

    /**
     * Set default options for rendering if it's needed
     *
     * @param array $options
     * @return array
     */
    public function getOptions(string $title, string $content, array $data = null): array
    {
        $options['content'] = 'layouts/' . $content;
        $options['title'] = $title;
        $options['header'] = 'layouts/header.phtml';
        $options['footer'] = 'layouts/footer.phtml';
        $options['user'] = $this->sessionData->getUser();
        $options['data'] = $data;
        return $options;
    }

    /**
     * Default method for render picture element
     *
     * @param string $type
     * @param string|null $value
     * @return string|null
     */
    public function renderElement(string $type, string $value = null)
    {
        if ($type == 'image') {
            $value = isset($value) ? "<img src='/Media/images/" . $value . "' width='50' height='50' alt='$value'>" : $value;
        }
        return $value;
    }
}
