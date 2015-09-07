<?php
namespace model;

class LoginModel{

    private $correctUsername = "Admin";
    private $correctPassword = "Password";
    private $hashedPassword;

    public static $setSession = 'LoginModel::user';

    public function authenticate($username, $password){
        if($username == $this->correctUsername && $password == $this->correctPassword){
            $_SESSION[self::$setSession] = $username;
            return 'Hello! :)';
        }
    }

    public function isSessionSet(){
        return isset($_SESSION[self::$setSession]);
    }

}