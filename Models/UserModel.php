<?php
declare(strict_types=1);

namespace Models;

/**
 * @package Models
 */
class UserModel extends DataModel
{
    /**
     * Check type of user and login on site
     * @param string $login
     * @param string $password
     * @return int
     */
    public function login(string $login, string $password): int
    {
        $user = $this->userExist($login);
        if ($user && password_verify($password, $user[0]['password'])) {
            if (!isset($user[0]['approve_status']) || $user[0]['approve_status']) {
                $this->setSessionData($user[0]);
                if ($_SESSION['user']['type'] == 'students') {
                    return 3;
                }
                return 1;
            }
            return 2;
        }
        return 0;
    }

    /**
     * Check is user in database
     * @param string $login
     * @return array|false|int|mixed
     */
    public function userExist(string $login)
    {
        $condition = "`login` = '$login'";
        $field = ['id', 'login', 'password', 'name', 'approve_status', 'family_member', 'image'];
        $result = $this->selectData('users', $field, $condition);
        if (!$result) {
            $field = ['id', 'login', 'password', 'name', 'e_mail', 'image'];
            $result = $this->selectData('students', $field, $condition);
        }
        return $result;
    }

    /**
     * Create new user
     * @param array $data
     * @param string $table
     * @return bool|string
     */
    public function newUser(array $data, string $table)
    {
        $user = $this->userExist($data['login']);
        if ($user) {
            return 'exist';
        }
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $data['image'] = $this->moveUploadFile($table);
        } else {
            $data['image'] = $table . '/standart_avatar.jpg';
        }
        $result = $this->insertData($table, $data);
        if ($result) {
            return true;
        }
        return false;
    }

    /**
     * Get users list
     * @return array|false|int|mixed
     */
    public function getAllUsers()
    {
        $field = ['id', 'name', 'family_member', 'age', 'address', 'approve_status', 'image'];
        return $this->selectData('users', $field);
    }

    /**
     * Get users list depending on access rights
     * @param string $access
     * @return array|false|int|mixed
     */
    public function adminGetAllUsers(string $access)
    {
        $condition = ($access == 'admin') ? '`family_member` = \'children\'' : '`family_member` != \'head\'';
        $field = ['id', 'name', 'family_member'];
        return $this->selectData('users', $field, $condition);
    }

    /**
     * Get users list who wasnt allowed for login on site
     * @param string $access
     * @return array|false|int|mixed
     */
    public function adminGetUsersForApprove(string $access)
    {
        $condition = ($access == 'admin') ? '`family_member` = \'children\' AND `approve_status` = \'0\''
            : '`approve_status` = 0';
        $field = ['id', 'name', 'family_member'];
        return $this->selectData('users', $field, $condition);
    }

    /**
     * Delete user from database
     * @param array $data
     * @return false|int
     */
    public function deleteUser(array $data)
    {
        $id = implode(',', $data);
        $this->deleteFile('users', $id);
        return $this->deleteData('users', $id);
    }

    /**
     * Update "family member group" for a user
     * @param array $data
     * @return false|int
     */
    public function updateUser(array $data)
    {
        $field['family_member'] = array_pop($data);
        $id = implode(',', $data);
        $condition = '`id` IN (' . $id . ')';
        return $this->updateData('users', $field, $condition);
    }

    /**
     * Set status "allowed to login" for user
     * @param array $data
     * @return false|int
     */
    public function approveUser(array $data)
    {
        $id = implode(',', $data);
        $field['approve_status'] = 1;
        $condition = '`id` IN (' . $id . ')';
        return $this->updateData('users', $field, $condition);
    }

    /**
     * Chande avatar for a user
     * @param string $table
     * @return bool|int
     */
    public function changeAvatar(string $table)
    {
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $field = ['id', 'image'];
            $id = $_SESSION['user']['id'];
            $condition = "`id` = '$id'";
            $user = $this->selectData($table, $field, $condition);
            if (isset($user[0]['image']) && $user[0]['image'] != $table . '/standart_avatar.jpg') {
                $imgName = $this->moveUploadFile($table, $user[0]['image']);
                $_SESSION['user']['image'] = $imgName;
                return true;
            } else {
                $imgName = $this->moveUploadFile($table);
                $field = ['image' => $imgName];
                $_SESSION['user']['image'] = $imgName;
                return $this->updateData($table, $field, $condition);
            }
        }
        return false;
    }

    /**
     * Delete user's avatar from project folder and delete field in database with the avatar's title
     * @param string $table
     * @return false|int
     */
    public function deleteAvatar(string $table)
    {
        $this->deleteFile($table, $_SESSION['user']['id']);
        $field = ['image' => $table . '/standart_avatar.jpg'];
        $condition = "`id` = " . $_SESSION['user']['id'];
        $_SESSION['user']['image'] = $table . '/standart_avatar.jpg';
        return $this->updateData($table, $field, $condition);
    }

    /**
     * Set session data for a user
     * @param array $user
     * @return void
     */
    private function setSessionData(array $user): void
    {
        foreach ($user as $key => $value) {
            if ($key == 'password') {
                continue;
            } elseif ($key == 'family_member' && ($value == 'head' || $value == 'admin')) {
                $_SESSION['user']['access'] = $value;
                continue;
            }
            $_SESSION['user'][$key] = $value;
        }
        $_SESSION['user']['type'] = isset($user['family_member']) ? 'family' : 'students';
    }
}
