<?php

namespace Controllers;

use Models\UserModel;
use View\UserView;

class UserController
{
    private $userModel;
    private $userView;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userView = new UserView();
    }

    public function loginAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $result = $this->userModel->login($login, $password);
            switch ($result) {
                case 1 :
                    $this->userView->location('/');
                    break;
                case 2 :
                    $errMsg = 'Мама пока не дала разрешение на вход!';
                    $this->userView->render(null, $errMsg);
                    break;
                case 0 :
                    $errMsg = 'Неверный логин или пароль!';
                    $this->userView->render(null, $errMsg);
            }
        } else {
            $errMsg = 'Простите, но что-то пошло не так. Попробуйте еще раз';
            $this->userView->render(null, $errMsg);
        }
    }

    public function logoutAction()
    {
        if ($this->userModel->isSigned()) {
            session_destroy();
        }
        $this->userView->location('/');
    }

    public function registrationAction($errMsg = null)
    {
        $isSigned = $this->userModel->isSigned();
        if (!$isSigned) {
            $options = [
                'title' => 'Регистрация',
                'content' => 'registration.phtml'
            ];
            $this->userView->render($options, $errMsg);
        } else {
            $this->userView->location('/');
        }
    }

    public function newAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->userModel->newUser($_POST);
        }
        if ($result === 'exist') {
            $message = ('Пользователь с таким логином уже зарегистрирован.');
        } elseif ($result) {
            $message = ('Вы успешно зарегистрировались. Ожидайте подтверждения от мамы.');
        } else {
            $message = ('Произошла ошибка. Вы ввели некорректные данные.');
        }
        $this->registrationAction($message);
    }

    public function allUsersAction()
    {
        $isSigned = $this->userModel->isSigned();
        if ($isSigned) {
            $allUsers = $this->userModel->getAllUsers();
            $i = 0;
            $options = [
                'title' => 'Список пользователей',
                'content' => 'all_users.phtml',
                'allUsers' => $allUsers
            ];
            $this->userView->render($options);
        } else {
            $this->userView->location('/');
        }
    }

    public function deleteAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->userModel->deleteUser($_POST);
            if ($result) {
                $this->userView->location('/user/allUsers');
            }
        }
    }

    public function updateUserAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->userModel->updateUser($_POST);
            if ($result) {
                $this->userView->location('/user/allUsers');
            }
        }
    }

    public function approveUserAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->userModel->approveUser($_POST);
            if ($result) {
                $this->userView->location('/user/allUsers');
            }
        }
    }

    public function avatarFormAction($errMsg = null)
    {
        $isSigned = $this->userModel->isSigned();
        if ($isSigned) {
            $options = [
                'title' => 'Аватар',
                'content' => 'change_avatar.phtml'
            ];
            $this->userView->render($options, $errMsg);
        } else {
            $this->userView->location('/');
        }
    }

    public function changeAvatarAction()
    {
        $isSigned = $this->userModel->isSigned();
        if ($isSigned) {
            $result = $this->userModel->changeAvatar();
            if ($result) {
                $errMsg = 'Аватар успешно изменён';
            } else {
                $errMsg = 'Произошла ошибка';
            }
            $this->avatarFormAction($errMsg);
        } else {
            $this->userView->location('/');
        }
    }

    public function deleteAvatarAction()
    {
        $isSigned = $this->userModel->isSigned();
        if ($isSigned) {
            $result = $this->userModel->deleteAvatar();
            if ($result) {
                $errMsg = 'Аватар успешно удалён';
            } else {
                $errMsg = 'У вас стандартный аватар';
            }
            $this->avatarFormAction($errMsg);
        } else {
            $this->userView->location('/');
        }
    }
}
