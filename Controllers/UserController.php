<?php
declare(strict_types=1);

namespace Controllers;

use Models\DataRegistry;
use Models\UserModel;
use View\UserView;
use Interfaces\IDataManagement;
use Models\Post;

/**
 * @package Controllers
 */
class UserController extends BaseController
{
    private UserModel $userModel;

    private UserView $userView;

    /**
     * @var IDataManagement
     */
    private IDataManagement $sessionData;

    /**
     * Object for access to POST data
     *
     * @var Post\Manager
     */
    private IDataManagement $postData;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userView = new UserView();
        $register = DataRegistry::getInstance();
        $this->sessionData = $register->get('session');
        $this->postData = $register->get('post');
    }

    /**
     * Render form for authorization
     *
     * @return void
     */
    public function authorizationAction(): void
    {
        if (!$this->userModel->isSigned()) {
            $options = $this->userView->getOptions('Авторизация', 'login.phtml');
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Login action
     *
     * @return void
     */
    public function loginAction(): void
    {
        if ($this->postData->isPost()) {
            $login = $this->postData->getData()['login'];
            $password = $this->postData->getData()['password'];
            $result = $this->userModel->login($login, $password);
            switch ($result) {
                case 1 :
                    $this->homeLocation();
                    break;
                case 2 :
                    $data['errMsg'] = 'Мама пока не дала разрешение на вход!';
                    break;
                case 0 :
                    $data['errMsg'] = 'Неверный логин или пароль!';
                    break;
                case 3:
                    $this->location('/student/login');
            }
        } else {
            $data['errMsg'] = 'Простите, но что-то пошло не так. Попробуйте еще раз';
        }
        $options = $this->userView->getOptions('Авторизация', 'login.phtml', $data);
        $this->userView->render($options);
    }

    /**
     * Logout action
     *
     * @return void
     */
    public function logoutAction(): void
    {
        if ($this->userModel->isSigned()) {
            $this->sessionData->destroy();
        }
        $this->homeLocation();
    }

    /**
     * Render form for registration
     *
     * @param array|null $params
     * @return void
     */
    public function registrationAction(array $params = null): void
    {
        if (!$this->userModel->isSigned()) {
            if (isset($params) && ($params[0] == 'family' || $params[0] == 'student')) {
                $data['user_type'] = $params[0];
            }
            $options = $this->userView->getOptions('Регистрация', 'registration.phtml', $data);
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Create new user
     *
     * @param array $params
     * @return void
     */
    public function newAction(array $params): void
    {
        if ($this->postData->isPost()) {
            $result = $this->userModel->newUser($this->postData->getData(), $params[0]);
            if ($result === 'exist') {
                $data['errMsg'] = ('Пользователь с таким логином уже зарегистрирован.');
            } elseif ($result) {
                $data['errMsg'] = ('Вы успешно зарегистрировались.');
            } else {
                $data['errMsg'] = ('Произошла ошибка. Вы ввели некорректные данные.');
            }
            $options = $this->userView->getOptions('Регистрация', 'registration.phtml', $data);
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form for change or delete avatar
     *
     * @return void
     */
    public function avatarFormAction(): void
    {
        if ($this->userModel->isSigned()) {
            $options = $this->userView->getOptions('Аватар', 'change_avatar.phtml');
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Change avatar action
     *
     * @param array $param
     * @return void
     */
    public function changeAvatarAction(array $param): void
    {
        if ($this->userModel->isSigned()) {
            $result = $this->userModel->changeAvatar($param[0]);
            if ($result) {
                $data['errMsg'] = 'Аватар успешно изменён';
            } else {
                $data['errMsg'] = 'Произошла ошибка';
            }
            $options = $this->userView->getOptions('Аватар', 'change_avatar.phtml', $data);
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete avatar action
     *
     * @param array $param
     * @return void
     */
    public function deleteAvatarAction(array $param): void
    {
        if ($this->userModel->isSigned()) {
            $result = $this->userModel->deleteAvatar($param[0]);
            if ($result) {
                $data['errMsg'] = 'Аватар успешно удалён';
            } else {
                $data['errMsg'] = 'У вас стандартный аватар';
            }
            $options = $this->userView->getOptions('Аватар', 'change_avatar.phtml', $data);
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }
}
