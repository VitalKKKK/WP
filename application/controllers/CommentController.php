<?php

class CommentController extends Controller
{
    public function __construct() {
        parent::__construct();
        $this->_model('comments');
    }

    function page() {
        $page_id = 1;
        $comments = $this->comments->getByPageID($page_id);
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