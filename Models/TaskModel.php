<?php

namespace Models;

class TaskModel
{
    public function __construct()
    {
        $this->pdo = new \PDO('mysql:host=test.local;dbname=Family', 'snuff', 'kyzmi4');
    }

    public function createTask($timeCreated, $createdBy, $executor, $task, $status, $timeStart, $timeEnd, $approvedBy, $imgName)
    {
        $sql = "INSERT INTO Tasks (time_created, created_by, executor, task, status, time_start, time_end, approved_by, img_name) 
                VALUES ('$timeCreated', '$createdBy', '$executor', '$task', '$status', '$timeStart', '$timeEnd', '$approvedBy', '$imgName')";
        if ($this->pdo->exec($sql)){
            return true;
        }
        return false;
    }

    public function getAllTasks(){
        $sql = "SELECT * from Tasks";
        $result = $this->pdo->query($sql);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleteTask($id){
        foreach ($_SESSION["allTasks"] as $task){
            if ($task["id"] == $id){
                unlink("Media/images/tasks/" . $task["img_name"]);
            }
        }
        $sql = "DELETE FROM Tasks WHERE id IN ($id)";
        $this->pdo->exec($sql);
    }

    public function approveTask($id){
        $name = $_SESSION["login"];
        $sql = "UPDATE Tasks SET status='Подтверждено', approved_by='$name' WHERE id='$id'";
      return  $this->pdo->exec($sql);
    }

    public function updateTask($id, $task){
        $sql = "UPDATE Tasks SET task='$task' WHERE id='$id'";
        return  $this->pdo->exec($sql);
    }

    public function getMyTasks($executor){
        $sql = "SELECT * FROM Tasks WHERE executor = '$executor' AND status !='выполнено' AND status !='подтверждено'";
        $result = $this->pdo->query($sql);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function userExecTask($id, $comment){
        $sql = "UPDATE Tasks SET status='выполнено', comment='$comment' WHERE id='$id'";
        return  $this->pdo->exec($sql);
    }

    public function getDoneTasks(){
        $sql = "SELECT * FROM Tasks WHERE status ='выполнено' OR status ='Подтверждено'";
        $result = $this->pdo->query($sql);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getFailTasks(){
        $time = time();
        $sql = "SELECT * FROM Tasks WHERE time_end < $time AND status !='выполнено' AND status !='подтверждено'";
        $result = $this->pdo->query($sql);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function restartTask($id, $timeStart, $timeEnd){
        $sql = "UPDATE Tasks SET time_start='$timeStart', time_end = '$timeEnd' WHERE id='$id'";
        return  $this->pdo->exec($sql);
    }
}