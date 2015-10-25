<?php
namespace model;

use common\CookieStorage;
use common\SessionStorage;

class LoginModel{
    private static $nameLocation = "User::name";
    private static $passwordLocation = "Password::name";

    private $variableToCrypt = "1dv408";
    private $hashedPassword;
    private $sessionStorage;
    private $persistentLoginDAL;
    private static $userID = "User::userIdentifier";

    public function __construct(){
        //Encrypt the password
        $this->hashedPassword = crypt($this->generateToken(), sha1($this->variableToCrypt));

        $this->persistentLoginDAL = new dal\PersistentLoginDAL();
        $this->sessionStorage = new SessionStorage();
        $this->cookieStorage =  new CookieStorage();
    }

    /**
     * Generate random token
     * @return string
     */
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

    public function isUserTheRightUser($userIdentifier){
        assert(is_string($userIdentifier));

        if($this->sessionStorage->isSessionSet(self::$userID)){
            return $this->sessionStorage->getSession(self::$userID) === $userIdentifier;
        }
        else{
            $this->sessionStorage->setSession(self::$userID, $userIdentifier);
            return null;
        }
    }

    //Save cookie on file
    public function savePersistentLogin($hash){
        assert(is_string($hash));
        $this->persistentLoginDAL->save($hash);
    }

    //Save expiration dates for name and password on file
    public function saveExpirationDate($howLongWillUserBeRemembered){
        $this->persistentLoginDAL->saveExpiration($howLongWillUserBeRemembered, $howLongWillUserBeRemembered);
    }

    /**
     * Get name expiration from file
     * @return mixed
     */
    public function getNameExpiration(){
        return $this->persistentLoginDAL->loadNameExpiration();
    }

    /**
     * Get password expiration from file
     * @return mixed
     */
    public function getPasswordExpiration(){
        return $this->persistentLoginDAL->loadPasswordExpiration();
    }

    /**
     * Get temporary password from file
     * @return string
     */
    public function getStoredPassword(){
        return $this->persistentLoginDAL->load();
    }

    public function getHashedPassword(){
        return $this->hashedPassword;
    }

    public function login($username, $password){
        assert(is_string($username) && is_string($password));
        $this->sessionStorage->setSession(self::$nameLocation, $username);
        $this->sessionStorage->setSession(self::$passwordLocation, $password);
        return true;
    }

    public function logout() {
        $this->sessionStorage->deleteSession(self::$nameLocation);
        $this->sessionStorage->deleteSession(self::$passwordLocation);
    }
}