<?php

namespace Models;

class UserModel
{
    public function __construct()
    {
        $this->pdo = new \PDO('mysql:host=test.local;dbname=Family', 'snuff', 'kyzmi4');
    }

    public function createUser($name, $status, $age, $address, $password, $approved, $imgName)
    {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user["name"] == $name) {
                return "Пользователь с таким именем уже зарегистрирован";
            }
        }
        $sql = "INSERT INTO Users (name, status, age, address, approve_status, password, image ) VALUES ('$name', '$status', '$age', '$address', '$approved', '$password', '$imgName')";
        if ($this->pdo->exec($sql)) {
            return "Заявка принята на рассмотрение. Ожидайте подтверждение от Мамы!";
        }
        return "Произошла ошибка при регистрации. Вы ввели некорректные данные";
    }

    public function userExist($login, $password)
    {
        $users = $this->getAllUsers();
        foreach ($users as $user) {
            if ($user["name"] == $login && password_verify($password, $user["password"]) && $user["approve_status"] == 1) {
                return true;
            } elseif ($user["name"] == $login && password_verify($password, $user["password"]) && $user["approve_status"] == 0) {
                return "Дождитесь подтверждения от мамы";
            }
        }
        return "Пользователь не найден или неверный пароль";
    }

    public function deleteUser($id)
    {
        foreach ($_SESSION["allUsers"] as $user){
            if ($user["id"] == $id){
                unlink("Media/images/users/" . $user["image"]);
            }
        }
        $sql = "DELETE FROM Users WHERE id IN ($id)";
        $this->pdo->exec($sql);
    }

    public function updateUserStatus($status, $id)
    {
        $sql = "UPDATE Users SET status='$status' WHERE id='$id'";
        $this->pdo->exec($sql);
    }

    public function getAllUsers()
    {
        $sql = "SELECT * FROM Users";
        $result = $this->pdo->query($sql);
        return $result->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function approveUser($id)
    {
        $sql = "UPDATE Users SET approve_status=true WHERE id='$id'";
        $this->pdo->exec($sql);
    }

    public function changeAvatar()
    {
        $imgName = "";
        if ($_FILES['imageUser']["error"] == UPLOAD_ERR_OK) {
            foreach ($_SESSION["allUsers"] as $users) {
                if ($users["name"] == $_SESSION['login']) {
                    if (isset($users['image'])) {
                        $imgName = $users["image"];
                        move_uploaded_file($_FILES['imageUser']['tmp_name'], 'Media/images/users/' . $imgName);
                        return true;
                    } else {
                        $imgName = rand() . $_FILES['imageUser']['name'];
                        move_uploaded_file($_FILES['imageUser']['tmp_name'], 'Media/images/users/' . $imgName);
                        $login = $_SESSION['login'];
                        $sql = "UPDATE Users SET image = '$imgName' WHERE name = '$login'";
                        return $this->pdo->exec($sql);
                    }
                }
            }
        }
        return false;
    }

    public function deleteAvatar()
    {
        foreach ($_SESSION['allUsers'] as $user) {
            if ($user['name'] == $_SESSION['login']) {
                unlink("Media/images/users/" . $user["image"]);
            }
        }
        $login = $_SESSION['login'];
        $sql = "UPDATE Users SET image = NULL WHERE name = '$login'";
        return $this->pdo->exec($sql);
    }

}