<?php

class CommentController extends Controller
{
    public $userID;
    public $userEmail;

    public function __construct() {
        parent::__construct();
        $this->_model('comments');

        $this->userID = (isset($_SESSION['user']['id'])) ? $_SESSION['user']['id'] : '';
    }

//    function form() {
//        echo $this->_view('_form', [], true);
//    }

    function page() {

        $userEmail = (isset($_SESSION['user']['email'])) ? $_SESSION['user']['email'] : '';
        if (!isset($_POST['url']) || empty($_POST['url'])) {
            echo json_encode(['status' => '0', 'error' => 'No path']);
        }
        $url = $_POST['url'];
        $path = parse_url($url, PHP_URL_PATH);
        $comments = $this->comments->getByPath($path);
        echo json_encode(['user_email' => $userEmail, 'comments' => $comments]);
    }

    function add() {
        if (!$this->userID) {
            echo json_encode(['status => 0', 'error' => 'Authorization error']);
            return;
        }
        $parent_id = 0;
        if (isset($_POST['parent_id'])) {
            $parent_id = $_POST['parent_id'];
        }

        if (!isset($_POST['message']) || empty($_POST['message'])
            || !isset($_POST['url']) || empty($_POST['url'])) {
            echo json_encode(['status => 0', 'error' => 'Error data']);
            return;
        }
        $path = parse_url($_POST['url'], PHP_URL_PATH);

        $comments = $this->comments;

        $comments->user_id = $this->userID;
        $comments->page_id = $this->comments->getPageID($path);;
        $comments->parent_id = $parent_id;

        $comments->message = $_POST['message'];
        $comments->time = time();

        $addComment = $comments->add();
        if ($addComment) {
            echo json_encode(['status' => 1, 'comment_id' => $addComment, 'comment_time' => date('Y-m-d H:i:s', $comments->time)]);
        } else {
            echo json_encode(['status' => 0]);
        }
        return;
    }


    function edit() {
        if (!isset($_POST['comment_id']) || empty($_POST['comment_id'])) {
            echo json_encode(['status' => '0', 'error' => 'No comment id']);
            return;
        }
        if (!isset($_POST['message'])) {
            echo json_encode(['status' => '0', 'error' => 'No message']);
            return;
        }

        $comment = $this->comments;
        $comment->id = $_POST['comment_id'];
        $comment->getByID();

        if ($comment->user_id != $this->userID) {
            echo json_encode(['status' => '0', 'error' => 'permission error']);
            return;
        }

        $comment->message = $_POST['message'];
        if ($comment->update()) {
            echo json_encode(['status' => '1']);
            return;
        }
    }


    function delete() {

        if (!$this->userID) {
            echo json_encode(['status' => '0', 'error' => 'Authorization error']);
            return;
        }
        if (!isset($_POST['comment_id']) || empty($_POST['comment_id'])) {
            echo json_encode(['status' => '0', 'error' => 'No comment id']);
            return;
        }

        $comment = $this->comments;
        $comment->id = $_POST['comment_id'];
        $comment->getByID();

        if ($comment->user_id != $this->userID) {
            echo json_encode(['status' => '0', 'error' => 'permission error']);
            return;
        }

        if ($comment->delete()) {
            echo json_encode(['status' => '1']);
            return;
        }
    }

    public function rating() {
        if (!$this->userID) {
            echo json_encode(['status' => '0', 'error' => 'Authorization error']);
            return;
        }

        $comment = $this->comments;
        $ratingData = [
            'add' => 1,
            'remove' => -1,
        ];

        if (!isset($_POST['comment_id']) || empty($_POST['comment_id']) ||
            !isset($_POST['rating']) || empty($_POST['rating']) || !isset($ratingData[$_POST['rating']])) {
            echo json_encode(['status' => '0', 'error' => 'Error data']);
            return;
        }

        $comment_id = $_POST['comment_id'];
        $comment->id = $comment_id;
        $comment->getByID();

        if ($comment->user_id == $this->userID) {
            echo json_encode(['status' => '0', 'error' => 'You can\'t rate own comments']);
            return;
        }

        $userCommentsData = $comment->getUserData($this->userID);

        $rating = $ratingData[$_POST['rating']];
        $commentRating = 0;
        if (isset($userCommentsData[$comment_id]) && !empty($userCommentsData[$comment_id])) {
            $commentRating = $userCommentsData[$comment_id];
        }

        if ($rating != $commentRating) {
            $userCommentsData[$comment_id] = $commentRating + $rating;
            $comment->rating += $rating;
        }

        if ($comment->updateRatingWithUser($this->userID, $userCommentsData)) {
            echo json_encode(['status' => '1', 'rating' => $comment->rating]);
            return;
        }
    }
}