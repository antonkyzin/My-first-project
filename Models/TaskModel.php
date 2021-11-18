<?php
declare(strict_types=1);

namespace Models;

/**
 * @package Models
 */
class TaskModel extends DataModel
{
    const STATUS_NEW = 3;
    const STATUS_DONE = 2;
    const STATUS_APPROVE = 1;
    const STATUS_FAIL = 0;

    /**
     * Get user's tasks list
     * @param bool $onlyNew
     * @return array|false|int|mixed
     */
    public function getMyTasks(bool $onlyNew)
    {
        $whereCondition = $onlyNew ? '`status` = ' . self::STATUS_NEW . ' AND `executor` = ' . $_SESSION['user']['id']
            : '`executor` = ' . $_SESSION['user']['id'];
        return $this->getAllTasks($whereCondition);
    }

    /**
     * Create new task
     * @param array $data
     * @return bool|int|mixed
     */
    public function createTask(array $data)
    {
        if ($_FILES['image']["error"] == UPLOAD_ERR_OK) {
            $data['image'] = $this->moveUploadFile('tasks');
        }
        $data['status'] = self::STATUS_NEW;
        return $this->insertData('tasks', $data);
    }

    /**
     * Get tasks list
     * @param string|null $whereCondition
     * @return array|false|int|mixed
     */
    public function getAllTasks(string $whereCondition = null)
    {
        $field = [
            't.id', 'time_created', 'u1.name AS created_by', 'u2.name AS executor', 'task',
            'status', 'time_start', 'time_end', 'comment', 'u3.name AS approved_by', 't.image'
        ];
        $joinCondition = [
            ['created_by' => 'u1.id',
                'executor' => 'u2.id',
                'approved_by' => 'u3.id']
        ];
        $joinTables = ['users'];
        return $this->selectJoinData('tasks', $joinTables, $field, $joinCondition, $whereCondition);
    }

    /**
     * Get tasks list depending on access rights for admin actions
     * @param string $access
     * @return array|false|int|mixed
     */
    public function adminGetAllTasks(string $access)
    {
        $whereCondition = ($access == 'admin') ? "u1.family_member = 'children'" : null;
        $field = ['t.id', 'u1.name AS executor', 'task'];
        $joinCondition = [
            ['executor' => 'u1.id']
        ];
        $joinTables = ['users'];
        return $this->selectJoinData('tasks', $joinTables, $field, $joinCondition, $whereCondition);
    }

    /**
     * Delete a task
     * @param array $data
     * @return false|int
     */
    public function deleteTask(array $data)
    {
        $id = implode(',', $data);
        $this->deleteFile('tasks', $id);
        return $this->deleteData('tasks', $id);
    }

    /**
     * Update a task
     * @param array $data
     * @return false|int
     */
    public function updateTask(array $data)
    {
        $task = array_pop($data);
        $id = implode(',', $data);
        $field = ['task' => $task];
        $condition = '`id` IN (' . $id . ')';
        return $this->updateData('tasks', $field, $condition);
    }

    /**
     * A student reports about executed a task
     * @param array $data
     * @return false|int
     */
    public function execTask(array $data)
    {
        $comment = array_pop($data);
        $id = implode(',', $data);
        $filed = [
            'status' => self::STATUS_DONE,
            'comment' => $comment
        ];
        $condition = '`id` IN (' . $id . ')';
        return $this->updateData('tasks', $filed, $condition);
    }

    /**
     * Get executed tasks list depending on access rights for admin actions
     * @param string $access
     * @return array|false|int|mixed
     */
    public function getDoneTasks(string $access)
    {
        $field = ['t.id', 'u1.name AS executor', 'task'];
        $joinCondition = [
            ['executor' => 'u1.id']
        ];
        $whereCondition = ($access == 'admin') ? "u1.family_member = 'children' AND status = " . self::STATUS_DONE
            : 'status = ' . self::STATUS_DONE;
        $joinTables = ['users'];
        return $this->selectJoinData('tasks', $joinTables, $field, $joinCondition, $whereCondition);
    }

    /**
     * Get failed tasks list depending on access rights for admin actions
     * @param string $access
     * @return array|false|int|mixed
     */
    public function getFailTasks(string $access)
    {
        $field = ['t.id', 'u1.name AS executor', 'task'];
        $joinCondition = [
            ['executor' => 'u1.id']
        ];
        $whereCondition = ($access == 'admin') ? "u1.family_member = 'children' AND status = " . self::STATUS_FAIL
            : 'status = ' . self::STATUS_FAIL;
        $joinTables = ['users'];
        return $this->selectJoinData('tasks', $joinTables, $field, $joinCondition, $whereCondition);
    }

    /**
     * Restart failed task
     * @param array $data
     * @return false|int
     */
    public function restartTask(array $data)
    {
        $timeEnd = array_pop($data);
        $id = implode(',', $data);
        $field = [
            'time_end' => $timeEnd,
            'status' => self::STATUS_NEW
        ];
        $condition = '`id` IN (' . $id . ')';
        return $this->updateData('tasks', $field, $condition);
    }

    /**
     * Set status done for a task
     * @param array $data
     * @return false|int
     */
    public function approveTask(array $data)
    {
        $id = implode(',', $data);
        $field = ['status' => self::STATUS_APPROVE,
            'approved_by' => $_SESSION['user']['id']
        ];
        $condition = '`id` IN (' . $id . ')';
        return $this->updateData('tasks', $field, $condition);
    }

    /**
     * Set status failed for a task if time for execution is out
     * @return false|int
     */
    public function updateStatus()
    {
        $field = ['status' => self::STATUS_FAIL];
        $condition = '`time_end` < NOW() AND `status` =' . self::STATUS_NEW;
        return $this->updateData('tasks', $field, $condition);
    }

    /**
     * Get users list for create new task
     * @param string $access
     * @return array|false|int|mixed
     */
    public function selectUsersForNewTask(string $access)
    {
        $field = ['id', 'name'];
        $condition = ($access == 'admin') ? "family_member = 'children'" : null;
        return $this->selectData('users', $field, $condition);
    }
}
