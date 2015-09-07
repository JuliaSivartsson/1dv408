<?php

namespace model;

class UserModel{

    private $username;
    private $password;
    private $users = array();

    public function __construct($username, $password){
        $this->username = $username;
        $this->password = $password;
    }

    public function register(UserModel $user){
        $this->users[] = $user;
    }
}