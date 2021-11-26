<?php
declare(strict_types=1);

namespace Controllers\Admin;

use Controllers\BaseController;
use Models\DataRegistry;
use Models\UserModel;
use View\UserView;
use Interfaces\IDataManagement;

/**
 * @package Controllers\Admin
 */
class UserController extends BaseController
{
    private UserModel $userModel;

    private UserView $userView;

    /**
     * Object for access to POST data
     */
    private IDataManagement $postData;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userView = new UserView();
        $this->postData = DataRegistry::getInstance()->get('post');
    }

    /**
     * Render admin user panel by param
     *
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
                    $title = 'Удаление';
                    $content = 'admin/delete_user.phtml';
                    break;
                case 'approve' :
                    $data = $this->userModel->adminGetUsersForApprove($access);
                    $title = 'Подтвердить';
                    $content = 'admin/approve_user.phtml';
                    break;
                case 'change' :
                    $data = $this->userModel->adminGetAllUsers($access);
                    $title = 'Изменить группу';
                    $content = 'admin/change_group_user.phtml';
                    break;

                default :
                    $this->homeLocation();
            }
            $options = $this->userView->getOptions($title, $content, $data);
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get all users list
     *
     * @return void
     */
    public function allUsersAction(): void
    {
        $access = $this->userModel->isAccess();
        if ($access) {
            $data = $this->userModel->getAllUsers();
            $options = $this->userView->getOptions('Список пользователей', 'admin/users.phtml', $data);
            $this->userView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete user from database
     *
     * @return void
     */
    public function deleteAction(): void
    {
        if ($this->postData->isPost()) {
            $result = $this->userModel->deleteUser($this->postData->getData());
            if ($result) {
                $this->location('/admin/user/allUsers');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Set permission to enter in user cabinet
     *
     * @return void
     */
    public function approveAction(): void
    {
        if ($this->postData->isPost()) {
            $result = $this->userModel->approveUser($this->postData->getData());
            if ($result) {
                $this->location('/admin/user/allUsers');
            }
        }
    }

    /**
     * Change access rights for users
     *
     * @return void
     */
    public function changeGroupAction(): void
    {
        if ($this->postData->isPost()) {
            $result = $this->userModel->updateUser($this->postData->getData());
            if ($result) {
                $this->location('/admin/user/allUsers');
            }
        }
    }
}
