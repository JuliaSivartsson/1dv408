<?php

namespace common;

session_start();

class SessionStorage{

    //public static $setSession = 'User::Name';

    //Check if session is set
    public function isSessionSet($key){
        return isset($_SESSION[$key]);
    }
    //Set session
    public function setSession($key, $value){
        $_SESSION[$key] = $value;
    }
    //Unset session
    public function deleteSession($key){
        unset($_SESSION[$key]);
    }

}