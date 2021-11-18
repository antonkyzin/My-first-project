<?php
declare(strict_types=1);

namespace Controllers;

use Models\UserModel;
use View\UserView;

/**
 * @package Controllers
 */
class UserController extends BaseController
{
    /**
     * @var UserModel
     */
    private UserModel $userModel;

    /**
     * @var UserView
     */
    private UserView $userView;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userView = new UserView();
    }

    /**
     * Render form for authorization
     * @return void
     */
    public function authorizationAction(): void
    {
        if (!$this->userModel->isSigned()) {
            $options = ['title' => 'Авторизация',
                'content' => 'login.phtml'
            ];
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Login action
     * @return void
     */
    public function loginAction(): void
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
                    break;
                case 0 :
                    $errMsg = 'Неверный логин или пароль!';
                    break;
                case 3:
                    $this->location('/student/login');
            }
        } else {
            $errMsg = 'Простите, но что-то пошло не так. Попробуйте еще раз';
        }
        $options = ['title' => 'Авторизация',
            'content' => 'login.phtml',
            'errMsg' => $errMsg
        ];
        $this->userView->render($options);
    }

    /**
     * Logout action
     * @return void
     */
    public function logoutAction(): void
    {
        if ($this->userModel->isSigned()) {
            session_destroy();
        }
        $this->homeLocation();
    }

    /**
     * Render form for registration
     * @param array|null $params
     * @return void
     */
    public function registrationAction(array $params = null): void
    {
        if (!$this->userModel->isSigned()) {
            $options = [
                'title' => 'Регистрация',
                'content' => 'registration.phtml'
            ];
            if (isset($params) && ($params[0] == 'family' || $params[0] == 'student')) {
                $options['param'] = $params[0];
            }
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Create new user
     * @param array $params
     * @return void
     */
    public function newAction(array $params): void
    {
        if ($this->checkPost()) {
            $result = $this->userModel->newUser($_POST, $params[0]);
            if ($result === 'exist') {
                $errMsg = ('Пользователь с таким логином уже зарегистрирован.');
            } elseif ($result) {
                $errMsg = ('Вы успешно зарегистрировались.');
            } else {
                $errMsg = ('Произошла ошибка. Вы ввели некорректные данные.');
            }
            $options = [
                'title' => 'Регистрация',
                'content' => 'registration.phtml',
                'errMsg' => $errMsg
            ];
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form for change or delete avatar
     * @return void
     */
    public function avatarFormAction(): void
    {
        if ($this->userModel->isSigned()) {
            $options = [
                'title' => 'Аватар',
                'content' => 'change_avatar.phtml'
            ];
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Change avatar action
     * @param array $param
     * @return void
     */
    public function changeAvatarAction(array $param): void
    {
        if ($this->userModel->isSigned()) {
            $result = $this->userModel->changeAvatar($param[0]);
            if ($result) {
                $errMsg = 'Аватар успешно изменён';
            } else {
                $errMsg = 'Произошла ошибка';
            }
            $options = [
                'title' => 'Аватар',
                'content' => 'change_avatar.phtml',
                'errMsg' => $errMsg
            ];
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete avatar action
     * @param array $param
     * @return void
     */
    public function deleteAvatarAction(array $param): void
    {
        if ($this->userModel->isSigned()) {
            $result = $this->userModel->deleteAvatar($param[0]);
            if ($result) {
                $errMsg = 'Аватар успешно удалён';
            } else {
                $errMsg = 'У вас стандартный аватар';
            }
            $options = [
                'title' => 'Аватар',
                'content' => 'change_avatar.phtml',
                'errMsg' => $errMsg
            ];
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }
}
