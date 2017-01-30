<?php

class Users extends Model
{
    public $id;
    public $email;
    public $password;

    public function add() {
        if (!isset($this->email) || !isset($this->password)) {
            return false;
        }

        $query = $this->pdo->prepare("INSERT INTO users (email, password) values (:email, :password)");
        $query->bindParam(':email', $this->email);
        $query->bindParam(':password', $this->password);
        return $query->execute();

    }

    public function getByEmail() {
        if (!$this->email) {
            return false;
        }

        $query = $this->pdo->prepare('SELECT * FROM users WHERE email = :email');
        $query->bindParam(':email', $this->email);
        $query->execute();
        $user = $query->fetch();

        if ($user) {
            $this->id = $user['id'];
            $this->password = $user['password'];
        }
        return;
    }


}