<?php

namespace Controllers;

use Models\UserModel;

class UserController implements IController
{
    private $model;
    private $fc;

    public function __construct()
    {
        $this->model = new UserModel();
        $this->fc = FrontController::getInstance();
    }

    public function createUserAction()
    {
        $imgName = "";
        if ($_FILES['imageUser']["error"] == UPLOAD_ERR_OK) {
            $imgName = rand() . $_FILES['imageUser']['name'];
            move_uploaded_file($_FILES['imageUser']['tmp_name'], 'Media/images/users/' . $imgName);
        }
        $name = $_POST["login"];
        $status = $_POST["status"];
        $age = $_POST["age"];
        $address = $_POST ["address"];
        $password = password_hash($_POST["password"], PASSWORD_BCRYPT);
        $approved = 0;
        $res = $this->model->createUser($name, $status, $age, $address, $password, $approved, $imgName);
        echo $res;
        header("Refresh:3; url=/");
    }

    public function userExistAction()
    {
        $login = $_POST["login"];
        $password = $_POST["password"];
        $res = $this->model->userExist($login, $password);
        if ($res === true) {
            $_SESSION["login"] = $_POST["login"];
            $_SESSION["allUsers"] = $this->model->getAllUsers();
            header("Location: /");
        }
        echo $res;
        header("Refresh:3; url=/");
    }

    public function deleteUserAction()
    {
        $id = implode(",", $_POST);
        $this->model->deleteUser($id);
        header("Location: /user/getAllUsers");
    }

    public function updateUserStatusAction()
    {
        $id = $_POST["id"];
        $status = $_POST["status"];
        $this->model->updateUserStatus($status, $id);
        header("Location: /user/getAllUsers");
    }


    public function getAllUsersAction()
    {
        $_SESSION["allUsers"] = $this->model->getAllUsers();
        header("Location: /view/render/opt/allUsers");
    }

    public function approveUserAction()
    {
        $params = $this->fc->getParams();
        $this->model->approveUser($params["id"]);
        header("Location: /user/getAllUsers");
    }

    public function logoutAction()
    {
        session_unset();
        header("Location: /");
    }

    public function changeAvatarAction()
    {
        $res = $this->model->changeAvatar();
        if ($res){
            header("Location: /");
        }
        echo "Выберите изображение";
        header("Refresh:3; url=/");
    }

    public function deleteAvatarAction(){
        $res =  $this->model->deleteAvatar();
        if ($res){
            header("Location: /");
        }
    }

}