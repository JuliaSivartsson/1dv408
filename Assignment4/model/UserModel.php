<?php

namespace model;

require_once('DAL/UserRepositoryDAL.php');
require_once('DAL/UserDatabaseDAL.php');

class UsernameTooShortException extends \Exception{};
class PasswordTooShort extends \Exception{};

class UserModel{

    private $username;
    private $password;
    private $userRepositoryDAL;
    private $database;
    private $existingNames;

    public function __construct($username, $password){
        if(strlen($username) < 3){
            throw new UsernameTooShortException();
        }
        if(strlen($password) < 6){
            throw new PasswordTooShort();
        }

        $this->userRepositoryDAL = new UserRepositoryDAL();
        $this->database = new UserDatabaseDAL();
        $this->username = $username;
        $this->password = $password;
    }

    public function saveNewUser(){
        $this->database->addUser($this->username, $this->password);
        return true;
    }

    /*
     * returns true if user already exists
     */
    public function doesUserAlreadyExists($username){
        $existingNames = $this->database->readAllUsernames();

        if(in_array($username,$existingNames)){
            return true;
        }
        return false;
    }

    public function getUsername(){
        return $this->username;
    }

    public function getPassword(){
        return $this->password;
    }
}