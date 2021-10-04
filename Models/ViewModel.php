<?php

namespace Models;

class ViewModel
{
    public function __construct()
    {

    }

    public function renderIndex()
    {
        ob_start();
        include_once("Views/header.phtml");
        if (!isset($_SESSION['login'])) {
            include_once "Views/index_view.phtml";
            include_once "Views/login.phtml";
        } else {
            echo "Вы вошли как " . $_SESSION["login"];
            foreach ($_SESSION["allUsers"] as $users) {
                if ($users["name"] == $_SESSION['login']) {
                    if (isset($users["image"])) {
                        echo "<img src='/Media/images/users/" . $users["image"] . "' width='50' height='50' alt='avatar'>";
                    } else {
                        echo "<img src='/Media/images/users/standart_avatar.jpg' width='50' height='50' alt='avatar'>";
                    }
                }
            }
            include_once "Views/menu_for_all.phtml";
        }
        include_once("Views/footer.phtml");
        return ob_get_clean();
    }

    public function render($param)
    {
        ob_start();
        echo $this->renderIndex();
        switch ($param) {
            case "registration" :
                include_once "Views/registration.phtml";
                break;
            case "allUsers" :
                include_once "Views/all_users.phtml";
                break;
            case "newTask" :
                include_once "Views/new_task.phtml";
                break;
            case "allTasks" :
                include_once "Views/all_tasks.phtml";
                break;
            case "myTasks" :
                include_once "Views/my_tasks.phtml";
                break;
            case "doneTasks" :
                include_once "Views/done_tasks.phtml";
                break;
            case "failTasks" :
                include_once "Views/fail_tasks.phtml";
                break;
            case "changeAvatar" :
                include_once "Views/change_avatar.phtml";
                break;
            case "404" :
                include_once "Views/404.phtml";
                break;
        }
        return ob_get_clean();
    }

}