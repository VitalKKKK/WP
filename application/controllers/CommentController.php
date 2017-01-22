<?php

class CommentController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->_model('comments');
    }

    function page() {
        if (!isset($_POST['url']) || empty($_POST['url'])) {
            echo json_encode(['status' => '0', 'error' => 'No path']);
        }
        $url = $_POST['url'];
        $path = parse_url($url, PHP_URL_PATH);
        $comments = $this->comments->getByPath($path);
        echo json_encode($comments);
    }

    function add() {
        $comments = $this->comments;

        $comments->user_id = 1;
        $comments->page_id = 1;
        $comments->parent_id = 0;
        $comments->message = 'message ' . time();
        $comments->time = time();

        $comments->add();
    }
}