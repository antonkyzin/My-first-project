<?php

namespace View;

class DefaultView
{
    public function render($options = null, $errMsg = null)
    {
        $options = $this->getOptions($options, $errMsg);
        include_once 'Templates/index.phtml';
    }

    public function getOptions($options, $errMsg)
    {
        $options['title'] = isset($options['title']) ? $options['title'] : 'Главная';
        $options['content'] = isset($options['content']) ? 'layouts/' . $options['content'] : 'layouts/main.phtml';
        $options['header'] = isset($options['header']) ? 'layouts/' . $options['header'] : 'layouts/header.phtml';
        $options['footer'] = isset($options['footer']) ? 'layouts/' . $options['footer'] : 'layouts/footer.phtml';
        $options['login'] = isset($_SESSION['login']) ? $_SESSION['login'] : null;
        if (isset($errMsg)) {
            $options['errMsg'] = $errMsg;
        }
        if (isset($_SESSION['login'])) {
            $options['menu'] = 'layouts/menu.phtml';
        }
        return $options;
    }

    public function location($url)
    {
        header("Location: $url");
    }

    public function renderCheckbox($login, $familyMember)
    {
        $result = false;
        if ($login == 'mother') {
            $result = true;
        } elseif ($login == 'father' && (mb_strtolower($familyMember) == 'сын' || mb_strtolower($familyMember) == 'дочь')) {
            $result = true;
        }
        return $result;
    }
}
