<?php

namespace Controllers;

use Models\TaskModel;
use Models\ViewModel;

class TaskController
{
    private $taskModel;
    private $viewModel;

    public function __construct()
    {
        $this->taskModel = new TaskModel();
        $this->viewModel = new ViewModel();
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
            $this->viewModel->render($options, $message);
        } else {
            $this->viewModel->location('/');
        }
    }

    public function createTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->taskModel->createTask($_POST);
            if ($result) {
                $this->newTaskAction('Задание успешно создано!');
            } else {
                $this->newTaskAction('Задание не создано. Заполните все поля!');
            }
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
                $this->viewModel->render($options);
            } else {
                $errMsg = 'Задания отсутствуют';
                $this->viewModel->render($options, $errMsg);
            }
        } else {
            $this->viewModel->location('/');
        }
    }

    public function deleteTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->taskModel->deleteTask($_POST);
            if ($result) {
                $this->viewModel->location('/task/allTasks');
            }
        }
    }

    public function updateTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->taskModel->updateTask($_POST);
            if ($result) {
                $this->viewModel->location('/task/allTasks');
            }
        }
    }

    public function myTasksAction()
    {
        $isSigned = $this->taskModel->isSigned();
        if ($isSigned) {
            $myTasks = $this->taskModel->getMyTasks();
            if ($myTasks) {
                $options = [
                    'title' => 'Список заданий',
                    'content' => 'my_tasks.phtml',
                    'myTasks' => $myTasks
                ];
                $this->viewModel->render($options);
            }
        } else {
            $this->viewModel->location('/');
        }
    }

    public function userExecTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->taskModel->execTask($_POST);
            if ($result) {
                $this->viewModel->location('/task/myTasks');
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
                $this->viewModel->render($options);
            } else {
                $errMsg = 'Нету выполненных заданий';
                $this->viewModel->render($options, $errMsg);
            }
        } else {
            $this->viewModel->location('/');
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
                $this->viewModel->render($options);
            } else {
                $errMsg = 'Нету проваленных заданий';
                $this->viewModel->render($options, $errMsg);
            }
        } else {
            $this->viewModel->location('/');
        }
    }

    public function restartTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->taskModel->restartTask($_POST);
            if ($result) {
                $this->viewModel->location('/task/getFailTasks');
            }
        }
    }

    public function approveTaskAction()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST)) {
            $result = $this->taskModel->approveTask($_POST);
            if ($result) {
                $this->viewModel->location('/task/getDoneTasks');
            }
        }
    }
}
