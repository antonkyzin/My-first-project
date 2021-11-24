<?php
declare(strict_types=1);

namespace View;

use http\Env\Request;

/**
 * Class for render course's table elements
 *
 * @package View
 */
class CourseView extends DefaultView
{
    /**
     * @param array $course
     * @param null|array $user
     * @return string
     */
    public function renderLink(array $course, array $user = null): string
    {
        if (!isset($user) || $user['type'] != 'students') {
            return '<a href = "/user/authorization">Зарегистрируйтесь как студент, что бы подать заявку</a>';
        }
        if (isset($user['courseClaim'])) {
            foreach ($user['courseClaim'] as $value) {
                $wqe = $value['course_id'];
                if ($wqe == $course['id']) {
                    return 'Вы уже подали заявку';
                }
            }
        }
        if (isset($user['groups'])) {
            foreach ($user['groups'] as $value) {
                if ($value['course'] == $course['id']) {
                    if (isset($_COOKIE['Alert']) && $_COOKIE['Alert'] == $course['id']) {
                        setcookie('Alert', '', time() - 3600, '/');
                        return 'Поздравляем! Вы приняты на курс!';
                    } else {
                        return 'Вы уже проходите данный курс';
                    }
                }
            }
        }
        return '<a href = "/course/addToCourseClaim/' . $user['id'] . '/' . $course['id'] . '">Подать заявку</a>';
    }

    /**
     * Render element depending on type
     *
     * @param string $type
     * @param string|null $value
     * @return string
     */
    public function renderElement(string $type, string $value = null): string
    {
        $value = parent::renderElement($type, $value);
        $bgColor = 'white';
        if ($type == 'status') {
            $bgColor = (!$value) ? 'red' : $bgColor;
            $value = $value ? 'Активна' : 'Закрыта';
        }
        return "<td bgcolor=\"$bgColor\">$value</td>";
    }
}
