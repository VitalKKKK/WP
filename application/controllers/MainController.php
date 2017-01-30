<?php

class MainController extends Controller
{
    function index() {
        $userID = (isset($_SESSION['user']['id'])) ? $_SESSION['user']['id'] : false;

        $this->_view('index', ['userID' => $userID]);
    }
}