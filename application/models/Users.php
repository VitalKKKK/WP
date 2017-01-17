<?php

class Users extends Model
{
    public function add() {
        $query = $this->pdo->prepare("INSERT INTO users (email, password) values ('email', 'password')");
        $query->execute();
    }
}