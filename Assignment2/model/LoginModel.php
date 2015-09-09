<?php
namespace model;

class LoginModel{

    private $correctUsername = "Admin";
    private $correctPassword = "Password";

    public static $setSession = 'User::Name';

    //Check if input matches correct credentials
    public function authenticate($username, $password){
        if($username === $this->correctUsername && $password === $this->correctPassword){
            return true;
        }
    }

    //Check if session is set
    public function isSessionSet(){
        return isset($_SESSION[self::$setSession]);
    }
    //Set session
    public function setSession($username){
        $_SESSION[self::$setSession] = $username;
    }
    //Unset session
    public function unsetSession(){
        unset($_SESSION[self::$setSession]);
    }

}