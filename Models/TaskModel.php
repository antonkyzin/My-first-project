<?php

namespace Models;

class TaskModel extends DatabaseModel
{
    const STATUS_NEW = 3;
    const STATUS_DONE = 2;
    const STATUS_APPROVE = 1;
    const STATUS_FAIL = 0;

    public function createTask($data)
    {
        if ($_FILES['image']["error"] == UPLOAD_ERR_OK) {
            $data['image'] = rand() . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], 'Media/images/tasks/' . $data['image']);
        }
        $data['time_end'] = date("Y-m-d H:i:s", time() + 3600 * $data['time_end']);
        $data['status'] = self::STATUS_NEW;
        return $this->insertData('tasks', $data);
    }

    public function getAllTasks($whereCondition = null)
    {
        $field = [
            't.id', 'time_created', 'u1.name AS created_by', 'u2.name AS executor', 'task',
            'status', 'time_start', 'time_end', 'comment', 'u3.name AS approved_by', 't.image'
        ];
        $joinCondition = [
            'created_by' => 'u1.id',
            'executor' => 'u2.id',
            'approved_by' => 'u3.id'
        ];
        $this->isTaskActive();
        return $this->selectJoinData('tasks', 'users', $field, $joinCondition, $whereCondition);
    }

    public function deleteTask(array $data)
    {
        $id = implode(',', $data);
        return $this->deleteData('tasks', $id);
    }

    public function updateTask($data)
    {
        $field = ['task' => $data['task']];
        $condition = '`id` = ' . $data['id'];
        return $this->updateData('tasks', $field, $condition);
    }

    public function getMyTasks()
    {
        $whereCondition = '`executor` = ' . $_SESSION['id'];
        return $this->getAllTasks($whereCondition);
    }

    public function execTask($data)
    {
        $filed = [
            'status' => self::STATUS_DONE,
            'comment' => $data['comment']
        ];
        $condition = '`id` = ' . $data['id'];
        return $this->updateData('tasks', $filed, $condition);
    }

    public function getDoneTasks()
    {
        $whereCondition = '`status` = ' . self::STATUS_DONE;
        return $this->getAllTasks($whereCondition);
    }

    public function isTaskActive()
    {
        $field = ['status' => self::STATUS_FAIL];
        $condition = '`time_end` < NOW()';
        return $this->updateData('tasks', $field, $condition);
    }

    public function getFailTasks()
    {
        $whereCondition = '`status` = ' . self::STATUS_FAIL;
        return $this->getAllTasks($whereCondition);
    }

    public function restartTask($data)
    {
        $data['time_end'] = date("Y-m-d H:i:s", time() + 3600 * $data['time_end']);
        $field = [
            'time_end' => $data['time_end'],
            'status' => self::STATUS_NEW
        ];
        $condition = '`id` = ' . $data['id'];
        return $this->updateData('tasks', $field, $condition);
    }

    public function approveTask($data)
    {
        $field = ['status' => self::STATUS_APPROVE];
        $condition = '`id` = '. $data['id'];
        return $this->updateData('tasks', $field, $condition);
    }
}
