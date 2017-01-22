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

    public function getByPath($path = false) {
        if (!$path) {
            return false;
        }
        $query = $this->pdo->prepare('SELECT id FROM comments_pages WHERE path = :path');
        $query->bindParam(':path', $path);
        $query->execute();
        $page = $query->fetch();

        if (!isset($page['id']) || empty($page['id'])) {
            return false;
        }
        $id = $page['id'];

        $query = $this->pdo->prepare('SELECT comments.*, users.email FROM comments LEFT JOIN users ON users.id = comments.user_id WHERE comments.page_id = :page_id ORDER BY comments.id DESC ');
        $query->bindParam(':page_id', $id);
        $query->execute();
        $comments = $query->fetchAll();

        $commentsData = [];
        foreach ($comments as $item) {
            $commentsData[$item['parent_id']][$item['id']] = [
                'id' => $item['id'],
                'message' => $item['message'],
                'date' => date('Y-m-d h:m:i', $item['time']),
            ];
        }

        return $commentsData;
    }


}