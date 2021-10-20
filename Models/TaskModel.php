<?php

namespace Models;

class TaskModel extends DataModel
{
    const STATUS_NEW = 3;
    const STATUS_DONE = 2;
    const STATUS_APPROVE = 1;
    const STATUS_FAIL = 0;

    public function createTask($data)
    {
        if ($_FILES['image']["error"] == UPLOAD_ERR_OK) {
            $data['image'] = $this->moveUploadFile('tasks');
        }
        $data['status'] = self::STATUS_NEW;
        return $this->insertData('tasks', $data);
    }

    public function getAllTasks($whereCondition = null)
    {
        $field = [
            't.id', 'time_created', 'u1.name AS created_by', 'u2.name AS executor', 'u3.family_member AS family_member', 'task',
            'status', 'time_start', 'time_end', 'comment', 'u4.name AS approved_by', 't.image'
        ];
        $joinCondition = [
            'created_by' => 'u1.id',
            'executor' => 'u2.id',
            't.executor' => 'u3.id',
            'approved_by' => 'u4.id'

        ];
        return $this->selectJoinData('tasks', 'users', $field, $joinCondition, $whereCondition);
    }

    public function deleteTask(array $data)
    {
        $id = implode(',', $data);
        $this->deleteFile('tasks', $id, 'tasks');
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

    public function updateTaskStatus()
    {
        $field = ['status' => self::STATUS_FAIL];
        $condition = '`time_end` < NOW() AND `status` =' . self::STATUS_NEW;
        $this->updateData('tasks', $field, $condition);
    }

    public function getFailTasks()
    {
        $whereCondition = '`status` = ' . self::STATUS_FAIL;
        return $this->getAllTasks($whereCondition);
    }

    public function restartTask($data)
    {
        $field = [
            'time_end' => $data['time_end'],
            'status' => self::STATUS_NEW
        ];
        $condition = '`id` = ' . $data['id'];
        return $this->updateData('tasks', $field, $condition);
    }

    public function approveTask($data)
    {
        $field = ['status' => self::STATUS_APPROVE,
            'approved_by' => $_SESSION['id']
        ];
        $condition = '`id` = ' . $data['id'];
        return $this->updateData('tasks', $field, $condition);
    }
}
