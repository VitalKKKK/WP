<?php

class UserController extends Controller
{

    public function __construct() {
        parent::__construct();
        $this->_model('users');
    }

    public function registration() {

        if ((!isset($_POST['email']) || empty($_POST['email']))
            || (!isset($_POST['password']) || empty($_POST['password']))
            || (!isset($_POST['confirm_password']) || empty($_POST['confirm_password']))) {
            echo json_encode(['status' => 0, 'message' => 'Error data']);
            return;
        }

        $user = $this->users;

        $user->email = $_POST['email'];
        $password = $_POST['password'];
        $confirm = $_POST['confirm_password'];
        $user->password = password_hash($password, PASSWORD_DEFAULT);

        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 0, 'message' => 'Email no valid']);
            return;
        }

        if ($password != $confirm) {
            echo json_encode(['status' => 0, 'message' => 'Error password & confirm']);
            return;
        }

        $user->getByEmail();

        if (isset($user->id) && !empty($user->id)) {
            echo json_encode(['status' => 0, 'message' => 'Email already exist']);
            return;
        }

        $this->users->add($user);
        echo json_encode(['status' => 1]);
    }


    public function login() {

        $user = $this->users;

        if ((!isset($_POST['email']) || empty($_POST['name']))
            && (!isset($_POST['password']) || empty($_POST['password']))) {
            echo json_encode(['status' => 0, 'message' => 'Error data']);
            return;
        }

        $user->email = $_POST['email'];
        $password = $_POST['password'];

        $user->getByEmail();
        if (!isset($user->id)) {
            echo json_encode(['status' => 0, 'message' => 'Email not found']);
            return;
        }

        if (!password_verify($password, $user->password)) {
            echo json_encode(['status' => 0, 'message' => 'Error password']);
            return;
        }

        $_SESSION['user']['id'] = $user->id;
        $_SESSION['user']['email'] = $user->email;

        echo json_encode(['status' => 1]);
    }


    public function logout() {
        session_unset();
        session_destroy();
        header('Location: http://' . $_SERVER['SERVER_NAME']);
        exit;
    }
}