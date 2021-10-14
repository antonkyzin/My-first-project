<?php

namespace Models;

class ViewModel
{

    public function render($options = null, $errMsg = null)
    {
        $options = $this->getOptions($options, $errMsg);
        include_once 'Templates/index.phtml';
    }

    public function getOptions($options, $errMsg)
    {
        $options['title'] = $options['title'] ?? 'Главная';
        $options['content'] = (!isset($options['content']) && !isset($_SESSION['login'])) ? 'main.phtml' : $options['content'];
        $options['header'] = $options['header'] ?? 'header.phtml';
        $options['footer'] = $options['footer'] ?? 'footer.phtml';
        $options['login'] = isset($_SESSION['login']) ? $_SESSION['login'] : null;
        if (isset($errMsg)) {
            $options['errMsg'] = $errMsg;
        }
        if (isset($_SESSION['login'])) {
            $options['menu'] = 'menu.phtml';
        }
        return $options;
    }

    public function location($url)
    {
        header("Location: $url");
    }
}
