<?php
declare(strict_types=1);

namespace Controllers;

use Models\DataRegistry;
use Models\TaskModel;
use View\TaskView;
use Interfaces\IDataManagement;

/**
 * @package Controllers
 */
class TaskController extends BaseController
{
    private TaskModel $taskModel;
    private TaskView $taskView;

    /**
     * Object for access to session data
     *
     * @var IDataManagement
     */
    private IDataManagement $sessionData;

    /**
     * Object for access to POST data
     *
     * @var IDataManagement
     */
    private IDataManagement $postData;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->taskView = new TaskView();
        $this->sessionData = DataRegistry::getInstance()->get('session');
        $this->postData = DataRegistry::getInstance()->get('post');
    }

    /**
     * Get tasks list for a user
     *
     * @return void
     */
    public function myTasksAction(): void
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned == 'family') {
            $data['tasks'] = $this->taskModel->getMyTasks(false);
            if (!$data) {
                $data['errMsg'] = 'Задания отсутствуют';
            }
            $options = $this->taskView->getOptions('Список заданий', 'user_main.phtml', $data);
            $this->taskView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Get tasks list
     *
     * @return void
     */
    public function allTasksAction(): void
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned == 'family') {
            $allTasks = $this->taskModel->getAllTasks();
            if (!$allTasks) {
                $data['errMsg'] = 'Задания отсутствуют';
            }
            $title = 'Список заданий';
            $data['tasks'] = $allTasks;
            $content = isset($this->sessionData->getUser()['access']) ? 'admin/tasks.phtml' : 'user_main.phtml';
            $options = $this->taskView->getOptions($title, $content, $data);
            $this->taskView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * Render form for reporting about executed a task
     *
     * @return void
     */
    public function doneFormAction(): void
    {
        if ($this->taskModel->isSigned() == 'family') {
            $data = $this->taskModel->getMyTasks(true);
            $options = $this->taskView->getOptions('Заявить о выполнении', 'done_task_form.phtml', $data);
            $this->taskView->render($options);
        } else {
            $this->homeLocation();
        }
    }

    /**
     * User report about executed a task
     *
     * @return void
     */
    public function userExecTaskAction(): void
    {
        if ($this->postData->isPost()) {
            $result = $this->taskModel->execTask($this->postData->getData());
            if ($result) {
                $this->location('/task/myTasks');
            }
        }
    }

    /**
     * Update status all tasks
     *
     * @return void
     */
    public function updateStatusAction(): void
    {
        $this->taskModel->updateStatus();
        $this->location('/task/allTasks');
    }
}
