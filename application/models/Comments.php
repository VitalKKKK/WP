<?php

class Comments extends Model
{
    public $id;
    public $user_id;
    public $page_id;
    public $parent_id;
    public $message;
    public $time;
    public $rating;

    public function add() {
        $query = $this->pdo->prepare("INSERT INTO comments (user_id, page_id, parent_id, message, time) VALUES (:user_id, :page_id, :parent_id, :message, :time)");
        $query->bindParam(':user_id', $this->user_id);
        $query->bindParam(':page_id', $this->page_id);
        $query->bindParam(':parent_id', $this->parent_id);
        $query->bindParam(':message', $this->message);
        $query->bindParam(':time', $this->time);
        if ($query->execute()) {
            return $this->pdo->lastInsertId();
        }

        return false;
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
            $message = $item['message'];
            $active = 1;
            if ($item['deleted']) {
                $message = 'This comment was deleted.';
                $active = 0;
            }

            $commentsData[$item['parent_id']][$item['id']] = [
                'id' => $item['id'],
                'email' => $item['email'],
                'message' => htmlentities($message),
                'date' => date('Y-m-d h:m:i', $item['time']),
                'rating' => $item['rating'],
                'active' => $active
            ];
        }

        return $commentsData;
    }

    public function getByID() {
        $query = $this->pdo->prepare('SELECT * FROM comments WHERE id = :id LIMIT 1');
        $query->bindParam(':id', $this->id);
        $query->execute();
        $comment = $query->fetch();


        if ($comment) {
            foreach ($comment as $key => $item) {
                $this->$key = $item;
            }
            return $comment;
        }

        return false;
    }

    public function delete() {

        $query = $this->pdo->prepare("UPDATE comments SET deleted = '1' WHERE id = :id");
        $query->bindParam(':id', $this->id);

        if ($query->execute()) {
            return true;
        }

        return false;
    }

    public function update() {

        $query = $this->pdo->prepare("UPDATE comments SET rating = :rating, message = :message, time = :time WHERE id = :id");
        $query->bindParam(':id', $this->id);
        $query->bindParam(':rating', $this->rating);
        $query->bindParam(':message', $this->message);
        $query->bindParam(':time', $this->time);

        if ($query->execute()) {
            return true;
        }

        return false;
    }

    public function getUserData($userID) {
        $query = $this->pdo->prepare('SELECT * FROM comments_users_data WHERE user_id = :user_id LIMIT 1');
        $query->bindParam(':user_id', $userID);
        $query->execute();
        $commentsData = $query->fetch();

        if ($commentsData) {
            return unserialize($commentsData['comments_data']);
        } else {
            $query = $this->pdo->prepare('INSERT INTO comments_users_data SET user_id = :user_id');
            $query->bindParam(':user_id', $userID);
            $query->execute();
        }

        return [];
    }

    public function updateRatingWithUser($userID, $commentsData) {
        try {
            $this->pdo->beginTransaction();
            $query = $this->pdo->prepare("UPDATE comments SET rating = :rating WHERE id = :id");
            $query->bindParam(':id', $this->id);
            $query->bindParam(':rating', $this->rating);
            $query->execute();


            $query = $this->pdo->prepare("UPDATE comments_users_data SET comments_data = :comments_data WHERE user_id = :user_id");
            $query->bindParam(':user_id', $userID);
            $query->bindParam(':comments_data', serialize($commentsData));
            $query->execute();

            $this->pdo->commit();

        } catch(Exception $e) {
            $this->pdo->rollBack();
            echo $e->getMessage();
            return false;
        }

        return true;
    }

    public function getPageID($path) {

        $query = $this->pdo->prepare('SELECT id FROM comments_pages WHERE path = :path');
        $query->bindParam(':path', $path);
        $query->execute();
        $page = $query->fetch();

        if (!isset($page['id']) || empty($page['id'])) {
            $query = $this->pdo->prepare('INSERT INTO comments_pages (path) VALUES (:path)');
            $query->bindParam(':path', $path);
            if ($query->execute()) {
                return $this->pdo->lastInsertId();
            }

            return false;
        }

        return $page['id'];
    }

}