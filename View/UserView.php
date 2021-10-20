<?php

namespace View;

class UserView extends DefaultView
{
    public function renderUserElement($type, $value)
    {
        switch ($type) {
            case 'image' :
                $result = isset($value) ? "<img src='/Media/images/users/" . $value . "' width='50' height='50' alt='$value'>" : $value;
                break;
            case 'approve_status' :
                $result = $value ? 'подтверждён' : 'не подтверждён';
                break;
            default :
                $result = $value;
        }
        return $result;
    }
}
