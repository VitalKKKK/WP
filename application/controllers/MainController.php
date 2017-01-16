<?php

class MainController extends Controller
{
    function index() {
        $this->_view('index', ['class' => __CLASS__]);
    }
}