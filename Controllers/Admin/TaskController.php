<?php
declare(strict_types=1);

namespace Controllers\Admin;

use Controllers\BaseController;
use Models\TaskModel;
use View\TaskView;

/**
 * @package Controllers\Admin
 */
class TaskController extends BaseController
{
    /**
     * @var TaskModel
     */
    private $taskModel;

    /**
     * @var TaskView
     */
    private $taskView;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->taskView = new TaskView();
    }

    /** Render admin panel by param
     * @param array $param
     */
    public function formAction(array $param): void
    {
        $access = $this->taskModel->isAccess();
        if ($access) {
            switch ($param[0]) {
                case 'delete' :
                    $data = $this->taskModel->adminGetAllTasks($access);
                    $options = ['title' => 'Удаление',
                        'content' => 'admin/delete_task.phtml',
                        'data' => $data];
                    break;
                case 'change' :
                    $data = $this->taskModel->adminGetAllTasks($access);
                    $options = ['title' => 'Изменить',
                        'content' => 'admin/change_task.phtml',
                        'data' => $data];
                    break;
                case 'approve' :
                    $data = $this->taskModel->getDoneTasks($access);
                    $options = ['title' => 'Подтвердить',
                        'content' => 'admin/approve_task.phtml',
                        'data' => $data];
                    break;
                case 'restart' :
                    $data = $this->taskModel->getFailTasks($access);
                    $options = ['title' => 'Возобновить',
                        'content' => 'admin/fail_task.phtml',
                        'data' => $data];
                    break;
                case 'new' :
                    $data = $this->taskModel->selectUsersForNewTask($access);
                    $options = ['title' => 'Новая задача',
                        'content' => 'admin/new_task.phtml',
                        'data' => $data];
                    break;
                default :
                    $this->homeLocation();
            }
            $this->taskView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Delete task
     * @return void
     */
    public function deleteAction(): void
    {
        if ($this->checkPost()) {
            $result = $this->taskModel->deleteTask($_POST);
            if ($result) {
                $this->location('/admin/task/form/delete');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Change task
     * @return void
     */
    public function changeAction(): void
    {
        if ($this->checkPost()) {
            $result = $this->taskModel->updateTask($_POST);
            if ($result) {
                $this->location('/task/allTasks');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Confirm done task
     * @return void
     */
    public function approveAction(): void
    {
        if ($this->checkPost()) {
            $result = $this->taskModel->approveTask($_POST);
            if ($result) {
                $this->location('/task/allTasks');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Restart failed task
     * @return void
     */
    public function restartAction(): void
    {
        if ($this->checkPost()) {
            $result = $this->taskModel->restartTask($_POST);
            if ($result) {
                $this->location('/task/allTasks');
            }
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Create new task
     * @return void
     */
    public function newAction(): void
    {
        if ($this->checkPost()) {
            $result = $this->taskModel->createTask($_POST);
            if ($result) {
                $this->location('/task/allTasks');
            }
        } else {
            $this->homeLocation();
        }
    }
}
