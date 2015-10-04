<?php

namespace model;

class UsernameTooShortException extends \Exception{};
class PasswordTooShort extends \Exception{};

class UserModel{

    private $username;
    private $password;
    private $userRepositoryDAL;
    private $database;

    public function __construct($username, $password){
        /**if(strlen($username) < 3){
            throw new UsernameTooShortException();
        }
        if(strlen($password) < 6){
            throw new PasswordTooShort();
        }*/

        $this->userRepository = new \model\dal\UserRepository();
        $this->username = $username;
        $this->password = $password;
    }

    public function saveNewUser(\model\UserModel $userToAdd){
        $this->userRepository->save($userToAdd);
        return true;
    }

    /*
     * returns true if user already exists
     */
    public function doesUserAlreadyExists($username){
        $existingNames = $this->userRepository->getAllUsers();

        foreach($existingNames as $users){
            if($users->getUsername() === $username){
                return true;
            }
        }
        return false;
    }

    public function authenticateLogin($username, $password){
        if(!is_string($username) || !is_string($password)){
            throw new \Exception("User::authenticateLogin needs two strings as parameters");
        }
        return ($this->username === $username && $this->password === $password);
    }

    public function getUsername(){
        return $this->username;
    }

    public function getPassword(){
        return $this->password;
    }
}