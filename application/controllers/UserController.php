<?php

class UserController extends Controller
{

    function registration() {
        $this->_model('Users');
        $this->users->add();
    }

}