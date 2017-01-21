<?php

class Comments extends Model
{
    public $id;
    public $user_id;
    public $page_id;
    public $parent_id;
    public $message;
    public $time;

    public function add() {
        $query = $this->pdo->prepare("INSERT INTO comments (user_id, page_id, parent_id, message, time) VALUES (:user_id, :page_id, :parent_id, :message, :time)");
        $query->bindParam(':user_id', $this->user_id);
        $query->bindParam(':page_id', $this->page_id);
        $query->bindParam(':parent_id', $this->parent_id);
        $query->bindParam(':message', $this->message);
        $query->bindParam(':time', $this->time);
        $query->execute();
    }

    public function getByPageID($id = false) {
        if (!$id) {
            return false;
        }
        $query = $this->pdo->prepare('SELECT comments.*, users.email FROM comments LEFT JOIN users ON users.id = comments.user_id ORDER BY comments.id DESC ');
        $query->execute();
        $comments = $query->fetchAll();

        $commentsData = [];
        foreach ($comments as $item) {
            $commentsData[$item['parent_id']][$item['id']] = $item;
        }

        return $commentsData;
    }


}