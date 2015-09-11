<?php

namespace common;

session_start();

//Class for managing sessions
class SessionStorage{

    //Check if session exists
    public function isSessionSet($sessionName){
        return isset($_SESSION[$sessionName]);
    }
    //Set session
    public function setSession($sessionName, $value){
        $_SESSION[$sessionName] = $value;
    }
    //Delete session
    public function deleteSession($sessionName){
        unset($_SESSION[$sessionName]);
    }

}