<?php
declare(strict_types=1);

namespace Controllers\Admin;

use Controllers\BaseController;
use Models\UserModel;
use View\UserView;

/**
 * @package Controllers\Admin
 */
class UserController extends BaseController
{
    /**
     * @var UserModel
     */
    private $userModel;

    /**
     * @var UserView
     */
    private $userView;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userView = new UserView();
    }

    /**
     * Render admin user panel by param
     * @param array $param
     * @return void
     */
    public function formAction(array $param): void
    {
        $access = $this->userModel->isAccess();
        if ($access) {
            switch ($param[0]) {
                case 'delete' :
                    $data = $this->userModel->adminGetAllUsers($access);
                    $options = ['title' => 'Удаление',
                        'content' => 'admin/delete_user.phtml',
                        'data' => $data];
                    break;
                case 'approve' :
                    $data = $this->userModel->adminGetUsersForApprove($access);
                    $options = ['title' => 'Подтвердить',
                        'content' => 'admin/approve_user.phtml',
                        'data' => $data];
                    break;
                case 'change' :
                    $data = $this->userModel->adminGetAllUsers($access);
                    $options = ['title' => 'Изменить группу',
                        'content' => 'admin/change_group_user.phtml',
                        'data' => $data];
                    break;

                default :
                    $this->homeLocation();
            }
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get all users list
     * @return void
     */
    public function allUsersAction(): void
    {
        $access = $this->userModel->isAccess();
        if ($access) {
            $data = $this->userModel->getAllUsers();
            $options = [
                'title' => 'Список пользователей',
                'content' => 'admin/users.phtml',
                'data' => $data
            ];
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete user from database
     * @return void
     */
    public function deleteAction(): void
    {
        if ($this->checkPost()) {
            $result = $this->userModel->deleteUser($_POST);
            if ($result) {
                $this->location('/admin/user/allUsers');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Set permission to enter in user cabinet
     * @return void
     */
    public function approveAction(): void
    {
        if ($this->checkPost()) {
            $result = $this->userModel->approveUser($_POST);
            if ($result) {
                $this->location('/admin/user/allUsers');
            }
        }
    }

    /**
     * Change access rights for users
     * @return void
     */
    public function changeGroupAction(): void
    {
        if ($this->checkPost()) {
            $result = $this->userModel->updateUser($_POST);
            if ($result) {
                $this->location('/admin/user/allUsers');
            }
        }
    }
}
