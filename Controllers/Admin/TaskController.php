<?php
declare(strict_types=1);

namespace Controllers\Admin;

use Controllers\BaseController;
use Models\DataRegistry;
use Models\TaskModel;
use View\TaskView;
use Interfaces\IDataManagement;

/**
 * @package Controllers\Admin
 */
class TaskController extends BaseController
{
    private TaskModel $taskModel;

    private TaskView $taskView;

    /**
     * Object for access to POST data
     */
    private IDataManagement $postData;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->taskView = new TaskView();
        $this->postData = DataRegistry::getInstance()->get('post');
    }

    /** Render admin panel by param
     *
     * @param array $param
     */
    public function formAction(array $param): void
    {
        $access = $this->taskModel->isAccess();
        if ($access) {
            switch ($param[0]) {
                case 'delete' :
                    $data = $this->taskModel->adminGetAllTasks($access);
                    $title = 'Удаление';
                    $content = 'admin/delete_task.phtml';
                    break;
                case 'change' :
                    $data = $this->taskModel->adminGetAllTasks($access);
                    $title = 'Изменить';
                    $content = 'admin/change_task.phtml';
                    break;
                case 'approve' :
                    $data = $this->taskModel->getDoneTasks($access);
                    $title = 'Подтвердить';
                    $content = 'admin/approve_task.phtml';
                    break;
                case 'restart' :
                    $data = $this->taskModel->getFailTasks($access);
                    $title = 'Возобновить';
                    $content = 'admin/fail_task.phtml';
                    break;
                case 'new' :
                    $data = $this->taskModel->selectUsersForNewTask($access);
                    $title = 'Новая задача';
                    $content = 'admin/new_task.phtml';
                    break;
                default :
                    $this->homeLocation();
            }
            $options = $this->taskView->getOptions($title, $content, $data);
            $this->taskView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete task
     *
     * @return void
     */
    public function deleteAction(): void
    {
        if ($this->postData->isPost()) {
            $result = $this->taskModel->deleteTask($this->postData->getData());
            if ($result) {
                $this->location('/admin/task/form/delete');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Change task
     *
     * @return void
     */
    public function changeAction(): void
    {
        if ($this->postData->isPost()) {
            $result = $this->taskModel->updateTask($this->postData->getData());
            if ($result) {
                $this->location('/task/allTasks');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Confirm done task
     *
     * @return void
     */
    public function approveAction(): void
    {
        if ($this->postData->isPost()) {
            $result = $this->taskModel->approveTask($this->postData->getData());
            if ($result) {
                $this->location('/task/allTasks');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Restart failed task
     *
     * @return void
     */
    public function restartAction(): void
    {
        if ($this->postData->isPost()) {
            $result = $this->taskModel->restartTask($this->postData->getData());
            if ($result) {
                $this->location('/task/allTasks');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Create new task
     *
     * @return void
     */
    public function newAction(): void
    {
        if ($this->postData->isPost()) {
            $result = $this->taskModel->createTask($this->postData->getData());
            if ($result) {
                $this->location('/task/allTasks');
            }
        } else {
            $this->homeLocation();
        }
    }
}
