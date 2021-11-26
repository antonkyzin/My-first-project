<?php
declare(strict_types=1);

namespace View;

/**
 * @package View
 */
class TaskView extends DefaultView
{
    /**
     * @param string $type
     * @param string|null $value
     * @return string|null
     */
    public function renderElement(string $type, string $value = null)
    {
        $value = parent::renderElement($type, $value);
        if ($type == 'status') {
            switch ($value) {
                case 3 :
                    $value = 'новое';
                    break;
                case 2 :
                    $value = 'выполнено';
                    break;
                case 1 :
                    $value = 'подтверждено';
                    break;
                default :
                    $value = 'провалено';
            }
        }
        return $value;
    }
}
