<?php

namespace common;

session_start();

//Class for managing sessions
class SessionStorage{

    public function __construct(){
        //Create a new session id in case of session hijacking
        session_regenerate_id();
    }

    public function isSessionSet($sessionName){
        return isset($_SESSION[$sessionName]);
    }

    public function getSession($sessionName){
        return $_SESSION[$sessionName];
    }

    public function setSession($sessionName, $value){
        assert(is_string($sessionName));
        $_SESSION[$sessionName] = $value;
    }

    public function deleteSession($sessionName){
        unset($_SESSION[$sessionName]);
    }

}