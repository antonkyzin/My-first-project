<?php

namespace Controllers;

use Models\UserModel;
use View\UserView;

class UserController extends BaseController
{
    private $userModel;
    private $userView;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userView = new UserView();
    }

    public function authorizationAction($errMsg = null)
    {
        if (!$this->userModel->isSigned()) {
            $options = ['title' => 'Авторизация',
                'content' => 'login.phtml'
            ];
            $this->userView->render($options, $errMsg);
        } else {
            $this->homeLocation();
        }
    }

    public function loginAction()
    {
        if ($this->checkPost()) {
            $login = $_POST['login'];
            $password = $_POST['password'];
            $result = $this->userModel->login($login, $password);
            switch ($result) {
                case 1 :
                    $this->homeLocation();
                    break;
                case 2 :
                    $errMsg = 'Мама пока не дала разрешение на вход!';
                    $this->authorizationAction($errMsg);
                    break;
                case 0 :
                    $errMsg = 'Неверный логин или пароль!';
                    $this->authorizationAction($errMsg);
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
        $this->homeLocation();
    }

    public function registrationAction($errMsg = null)
    {
        if (!$this->userModel->isSigned()) {
            $options = [
                'title' => 'Регистрация',
                'content' => 'registration.phtml'
            ];
            $this->userView->render($options, $errMsg);
        } else {
            $this->homeLocation();
        }
    }

    public function newAction()
    {
        if ($this->checkPost()) {
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
            $options = [
                'title' => 'Список пользователей',
                'content' => 'all_users.phtml',
                'allUsers' => $allUsers
            ];
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    public function deleteAction()
    {
        if ($this->checkPost()) {
            $result = $this->userModel->deleteUser($_POST);
            if ($result) {
                $this->location('/user/allUsers');
            }
        }
    }

    public function updateUserAction()
    {
        if ($this->checkPost()) {
            $result = $this->userModel->updateUser($_POST);
            if ($result) {
                $this->location('/user/allUsers');
            }
        }
    }

    public function approveUserAction()
    {
        if ($this->checkPost()) {
            $result = $this->userModel->approveUser($_POST);
            if ($result) {
                $this->location('/user/allUsers');
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
            $this->homeLocation();
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
            $this->homeLocation();
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
            $this->homeLocation();
        }
    }
}
