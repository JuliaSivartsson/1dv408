<?php
namespace model;

use common\SessionStorage;

require_once('DAL/PersistentLoginDAL.php');


class LoginModel{

    private static $nameLocation = "User::name";
    private static $passwordLocation = "Password::name";

    private $correctUsername = "Admin";
    private $correctPassword = "Password";

    private $variableToCrypt = "1dv408";
    private $hashedPassword;

    private $sessionStorage;
    private $persistentLoginDAL;

    public function __construct(){
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

    private function generateToken() {
        $token = "";
        for ($i=0; $i < 30; $i++) {
            $token .= mt_rand(0, 9);
        }
        return $token;
    }

    public function isUserSaved(){
        return $this->sessionStorage->isSessionSet(self::$nameLocation);
    }

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