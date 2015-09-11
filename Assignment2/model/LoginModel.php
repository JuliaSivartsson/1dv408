<?php
namespace model;

use common\SessionStorage;

require_once('DAL/PersistentLoginDAL.php');

class LoginModel{

    //The correct credentials for login
    private $correctUsername = "Admin";
    private $correctPassword = "Password";

    private static $nameLocation = "User::name";
    private static $passwordLocation = "Password::name";

    private $variableToCrypt = "1dv408";
    private $hashedPassword;
    private $sessionStorage;
    private $persistentLoginDAL;

    public function __construct(){
        //Encrypt the password
        $this->hashedPassword = crypt($this->generateToken(), sha1($this->variableToCrypt));

        $this->persistentLoginDAL = new PersistentLoginDAL();
        $this->sessionStorage = new SessionStorage();
    }

    //Check if input matches correct credentials
    public function authenticate($username, $password){
        if($username === $this->correctUsername && $password === $this->correctPassword){
            return true;
        }
    }

    //Generate random token for cookie
    private function generateToken() {
        $token = "";
        for ($i=0; $i < 30; $i++) {
            $token .= mt_rand(0, 9);
        }
        return $token;
    }

    //Does the session exists
    public function isUserSaved(){
        return $this->sessionStorage->isSessionSet(self::$nameLocation);
    }

    //Save cookie on file
    public function savePersistentLogin($hash){
        $this->persistentLoginDAL->save($hash);
    }

    public function getHashedPassword(){
        return $this->hashedPassword;
    }

    public function login($username, $password){
        $this->sessionStorage->setSession(self::$nameLocation, $username);
        $this->sessionStorage->setSession(self::$passwordLocation, $password);
    }

    public function logout() {
        $this->sessionStorage->deleteSession(self::$nameLocation);
        $this->sessionStorage->deleteSession(self::$passwordLocation);
    }
}