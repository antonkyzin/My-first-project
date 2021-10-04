<?php

namespace Controllers;

use Models\TaskModel;

class TaskController implements IController
{
    private $model;
    private $fc;

    public function __construct()
    {
        $this->model = new TaskModel();
        $this->fc = FrontController::getInstance();
    }

    public function createTaskAction()
    {
        $imgName = "";
        if ($_FILES['imageTask']["error"] == UPLOAD_ERR_OK) {
            $imgName = rand() . $_FILES['imageTask']['name'];
            move_uploaded_file($_FILES['imageTask']['tmp_name'], 'Media/images/tasks/' . $imgName);
        }
        $timeCreated = time();
        $createdBy = $_POST["created_by"];
        $executor = $_POST["executor"];
        $task = $_POST["task"];
        $status = "новое";
        $timeStart = time() + (int)$_POST["time_start"] * 3600;
        $timeEnd = $timeStart + (int)$_POST["time_end"] * 3600;
        $approvedBy = "Не подтверждена";
        $res = $this->model->createTask($timeCreated, $createdBy, $executor, $task, $status, $timeStart, $timeEnd, $approvedBy, $imgName);
        echo $res ? "Задание успешно добавлено!" : "Произошла ошибка. Корректно заполните все поля!";
        header("Refresh: 3; /view/render/opt/newTask");
    }

    public function getAllTasksAction()
    {
        $_SESSION["allTasks"] = $this->model->getAllTasks();
        header("Location: /view/render/opt/allTasks");
    }

    public function deleteTaskAction()
    {
        $id = implode(",", $_POST);
        $this->model->deleteTask($id);
        header("Location: /task/getAllTasks");
    }

    public function approveTaskAction()
    {
        $params = $this->fc->getParams();
        $res = $this->model->approveTask($params["id"]);
        if ($res) {
            header("Location: /task/getAllTasks");
        }
    }

    public function updateTaskAction()
    {
        $id = $_POST["id"];
        $task = $_POST["task"];
        $res = $this->model->updateTask($id, $task);
        if ($res) {
            header("Location: /task/getAllTasks");
        }
        echo "Изменение не выполнено";
        header("Refresh:3; /task/getAllTasks");
    }

    public function getMyTasksAction()
    {
        $executor = $_SESSION["login"];
        $_SESSION["myTasks"] = $this->model->getMyTasks($executor);
        header("Location: /view/render/opt/myTasks");
    }

    public function userExecTaskAction()
    {
        $comment = $_POST["comment"];
        $id = $_POST["id"];
        $res = $this->model->userExecTask($id, $comment);
        if ($res) {
            header("Location: /task/getMyTasks");
        }
        echo "Изменение не выполнено";
        header("Refresh:3; /task/getAllTasks");
    }

    public function getDoneTasksAction()
    {
        $_SESSION["doneTasks"] = $this->model->getDoneTasks();
        header("Location: /view/render/opt/doneTasks");
    }

    public function getFailTasksAction()
    {
        $_SESSION["failTasks"] = $this->model->getFailTasks();
        header("Location: /view/render/opt/failTasks");

    }

    public function restartTaskAction()
    {
        $timeStart = time() + (int)$_POST["timeStart"] * 3600;
        $timeEnd = $timeStart + (int)$_POST["timeEnd"] * 3600;
        $id = $_POST["id"];
        $res = $this->model->restartTask($id, $timeStart, $timeEnd);
        if ($res){
            header("Location: /task/getFailTasks");
        }
    }
}