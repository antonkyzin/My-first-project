<?php

namespace Models;

class UserModel extends DatabaseModel
{
    public function login($login, $password)
    {
        $user = $this->userExist($login);
        if ($user && password_verify($password, $user[0]['password']) && ($user[0]['approve_status'])) {
            $_SESSION['login'] = $login;
            $_SESSION['id'] = $user[0]['id'];
            return 1;
        } elseif ($user && !$user[0]['approve_status']) {
            return 2;
        }
        return 0;
    }

    public function userExist($login)
    {
        $field = ['id', 'login', 'password', 'approve_status'];
        $condition = "`login` = '$login'";
        return $this->selectData('users', $field, $condition);
    }

    public function newUser(array $data)
    {
        $user = $this->userExist($data['login']);
        if ($user) {
            return 'exist';
        }
        $data['password'] = password_hash($data['password'], PASSWORD_BCRYPT);
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $data['image'] = rand() . $_FILES['image']['name'];
            move_uploaded_file($_FILES['image']['tmp_name'], 'Media/images/users/' . $data['image']);
        } else {
            $data['image'] = 'standart_avatar.jpg';
        }
        $result = $this->insertData('users', $data);
        if ($result) {
            return true;
        }
        return false;
    }

    public function getAllUsers()
    {
        $field = ['id', 'name', 'family_member', 'age', 'address', 'approve_status', 'image'];
        return $this->selectData('users', $field);
    }

    public function deleteUser(array $data)
    {
        $id = implode(',', $data);
        return $this->deleteData('users', $id);
    }

    public function updateUser(array $data)
    {
        $field['family_member'] = $data['status'];
        $condition = '`id` = ' . $data['id'];
        return $this->updateData('users', $field, $condition);

    }

    public function approveUser($data)
    {
        $field['approve_status'] = 1;
        $condition = '`id` = ' . $data['id'];
        return $this->updateData('users', $field, $condition);
    }

    public function changeAvatar()
    {
        $imgName = '';
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $field = ['login', 'image'];
            $login = $_SESSION['login'];
            $condition = "`login` = '$login'";
            $user = $this->selectData('users', $field, $condition);
            if (isset($user[0]['image'])) {
                $imgName = $user[0]['image'];
                move_uploaded_file($_FILES['image']['tmp_name'], 'Media/images/users/' . $imgName);
                return true;
            } else {
                $imgName = rand() . $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], 'Media/images/users/' . $imgName);
                $field = ['image' => $imgName];
                return $this->updateData('users', $field, $condition);
            }
        }
        return false;
    }

    public function deleteAvatar()
    {
        $field = ['login', 'image'];
        $login = $_SESSION['login'];
        $condition = "`login` = '$login'";
        $user = $this->selectData('users', $field, $condition);
        $imgName = $user[0]["image"];
        unlink("Media/images/users/" . $imgName);
        $field = ['image' => 'standart_avatar.jpg'];
        return $this->updateData('users', $field, $condition);
    }
}
