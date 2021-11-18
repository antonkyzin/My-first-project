<?php
declare(strict_types=1);

namespace View;

/**
 * Default class for extends
 * @package View
 */
class DefaultView
{
    /**
     * Maim method for rendering data
     * @param null|array $options
     * @return void
     */
    public function render(array $options = null): void
    {
        $options = $this->getOptions($options);
        include_once 'Templates/index.phtml';
    }

    /**
     * Set default options for rendering if it's needed
     * @param array $options
     * @return array
     */
    public function getOptions(array $options): array
    {
        $options['content'] = 'layouts/' . $options['content'];
        $options['title'] = isset($options['title']) ? $options['title'] : 'Главная';
        $options['header'] = isset($options['header']) ? 'layouts/' . $options['header'] : 'layouts/header.phtml';
        $options['footer'] = isset($options['footer']) ? 'layouts/' . $options['footer'] : 'layouts/footer.phtml';
        $options['user'] = isset($_SESSION['user']) ? $_SESSION['user'] : null;
        return $options;
    }

    /**
     * Default method for render picture element
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
