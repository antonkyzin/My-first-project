<?php
declare(strict_types=1);

namespace View;

/**
 * @package View
 */
class UserView extends DefaultView
{
    /**
     * Render a user element
     *
     * @param string $type
     * @param string|null $value
     * @return string|null
     */
    public function renderElement(string $type, string $value = null)
    {
        $value = parent::renderElement($type, $value);
        if ($type == 'approve_status') {
            $value = $value ? 'подтверждён' : 'не подтверждён';
        }
        return $value;
    }
}
