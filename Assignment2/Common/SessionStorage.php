<?php

namespace common;

session_start();

//Class for managing sessions
class SessionStorage{

    public function isSessionSet($sessionName){
        return isset($_SESSION[$sessionName]);
    }

    public function setSession($sessionName, $value){
        $_SESSION[$sessionName] = $value;
    }

    public function deleteSession($sessionName){
        unset($_SESSION[$sessionName]);
    }

}