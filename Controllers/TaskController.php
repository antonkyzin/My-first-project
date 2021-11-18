<?php
declare(strict_types=1);

namespace Controllers;

use Models\TaskModel;
use View\TaskView;

/**
 * @package Controllers
 */
class TaskController extends BaseController
{
    /**
     * @var TaskModel
     */
    private TaskModel $taskModel;

    /**
     * @var TaskView
     */
    private TaskView $taskView;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->taskView = new TaskView();
    }

    /**
     * Get tasks list for a user
     * @return void
     */
    public function myTasksAction(): void
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned == 'family') {
            $myTasks = $this->taskModel->getMyTasks(false);
            $options = [
                'title' => 'Список заданий',
                'content' => 'user_main.phtml',
                'data' => $myTasks
            ];
            if (!$myTasks) {
                $options['errMsg'] = 'Задания отсутствуют';
            }
            $this->taskView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get tasks list
     * @return void
     */
    public function allTasksAction(): void
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned == 'family') {
            $allTasks = $this->taskModel->getAllTasks();
            $options = [
                'title' => 'Список заданий',
                'data' => $allTasks
            ];
            $options['content'] = isset($_SESSION['user']['access']) ? 'admin/tasks.phtml' : 'user_main.phtml';
            if (!$allTasks) {
                $options['errMsg'] = 'Задания отсутствуют';
            }
            $this->taskView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form for reporting about executed a task
     * @return void
     */
    public function doneFormAction(): void
    {
        if ($this->taskModel->isSigned() == 'family') {
            $data = $this->taskModel->getMyTasks(true);
            $options = ['title' => 'Заявить о выполнении',
                'content' => 'done_task_form.phtml',
                'data' => $data];
            $this->taskView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * User report about executed a task
     * @return void
     */
    public function userExecTaskAction(): void
    {
        if ($this->checkPost()) {
            $result = $this->taskModel->execTask($_POST);
            if ($result) {
                $this->location('/task/myTasks');
            }
        }
    }

    /**
     * Update status all tasks
     * @return void
     */
    public function updateStatusAction(): void
    {
        $this->taskModel->updateStatus();
        $this->location('/task/allTasks');
    }
}
