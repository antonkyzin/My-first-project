<?php

namespace Controllers;

use Models\TaskModel;
use View\TaskView;

class TaskController
{
    private $taskModel;
    private $taskView;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->taskView = new TaskView();
    }

    public function newTaskAction($message = null)
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned) {
            $usersName = $this->taskModel->selectData('users', ['id', 'name', 'family_member']);
            $options = [
                'title' => 'Новое задание',
                'content' => 'new_task.phtml',
                'usersName' => $usersName
            ];
            $this->taskView->render($options, $message);
        } else {
            $this->taskView->location('/');
        }
    }

    public function createTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->taskModel->createTask($_POST);
            if ($result) {
                $message = ('Задание успешно создано!');
            } else {
                $message = ('Задание не создано. Заполните все поля!');
            }
            $this->newTaskAction($message);
        }
    }

    public function allTasksAction()
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned) {
            $allTasks = $this->taskModel->getAllTasks();
            $options = [
                'title' => 'Список заданий',
                'content' => 'all_tasks.phtml',
                'allTasks' => $allTasks
            ];
            if ($allTasks) {
                $this->taskView->render($options);
            } else {
                $errMsg = 'Задания отсутствуют';
                $this->taskView->render($options, $errMsg);
            }
        } else {
            $this->taskView->location('/');
        }
    }

    public function deleteTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->taskModel->deleteTask($_POST[]);
            if ($result) {
                $this->taskView->location('/task/allTasks');
            }
        } else {
            $this->taskView->location('/task/allTasks');
        }
    }

    public function updateTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->taskModel->updateTask($_POST);
            if ($result) {
                $this->taskView->location('/task/allTasks');
            }
        }
    }

    public function myTasksAction()
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned) {
            $myTasks = $this->taskModel->getMyTasks();
            $options = [
                'title' => 'Список заданий',
                'content' => 'my_tasks.phtml',
                'myTasks' => $myTasks
            ];
            if ($myTasks) {
                $this->taskView->render($options);
            } else {
                $errMsg = 'Задания отсутствуют';
                $this->taskView->render($options, $errMsg);
            }
        } else {
            $this->taskView->location('/');
        }
    }

    public function userExecTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->taskModel->execTask($_POST);
            if ($result) {
                $this->taskView->location('/task/myTasks');
            }
        }
    }

    public function getDoneTasksAction()
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned) {
            $doneTasks = $this->taskModel->getDoneTasks();
            $options = ['title' => 'Список заданий',
                'content' => 'done_tasks.phtml',
                'doneTasks' => $doneTasks
            ];
            if ($doneTasks) {
                $this->taskView->render($options);
            } else {
                $errMsg = 'Нету выполненных заданий';
                $this->taskView->render($options, $errMsg);
            }
        } else {
            $this->taskView->location('/');
        }
    }

    public function getFailTasksAction()
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned) {
            $failTasks = $this->taskModel->getFailTasks();
            $options = [
                'title' => 'Список заданий',
                'content' => 'fail_tasks.phtml',
                'failTasks' => $failTasks
            ];
            if ($failTasks) {
                $this->taskView->render($options);
            } else {
                $errMsg = 'Нету проваленных заданий';
                $this->taskView->render($options, $errMsg);
            }
        } else {
            $this->taskView->location('/');
        }
    }

    public function restartTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->taskModel->restartTask($_POST);
            if ($result) {
                $this->taskView->location('/task/getFailTasks');
            }
        }
    }

    public function approveTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && !empty($_POST)) {
            $result = $this->taskModel->approveTask($_POST);
            if ($result) {
                $this->taskView->location('/task/getDoneTasks');
            }
        }
    }

    public function updateTaskStatusAction()
    {
        $this->taskModel->updateTaskStatus();
        $this->taskView->location('/task/allTasks');
    }
}
