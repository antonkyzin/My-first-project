<?php

namespace View;

class TaskView extends DefaultView
{
    public function renderTaskElement($type, $value)
    {
        switch ($type) {
            case 'image' :
                $result = isset($value) ? "<img src='/Media/images/tasks/" . $value . "' width='50' height='50' alt='$value'>" : $value;
                break;
            case 'status' :
                switch ($value) {
                    case 3 :
                        $result = 'новое';
                        break;
                    case 2 :
                        $result = 'выполнено';
                        break;
                    case 1 :
                        $result = 'подтверждено';
                        break;
                    default :
                        $result = 'провалено';
                }
                break;
            default :
                $result = $value;
        }
        return $result;
    }
}
