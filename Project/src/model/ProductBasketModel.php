<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-04
 * Time: 12:36
 */

namespace model;


class ProductBasketModel
{

    private static $nameLocation = "Basket::username";
    private static $passwordLocation = "Basket::product";

    private $hashedPassword;
    private $sessionStorage;
    private $persistentBasketDAL;
    private static $userID = "User::userIdentifier";

    public function __construct(){
        //Encrypt the password
        //$this->hashedPassword = crypt($this->generateToken(), sha1($this->variableToCrypt));

        $this->persistentBasketDAL = new \model\dal\ProductBasketDAL();
        $this->sessionStorage = new \common\SessionStorage();
        $this->cookieStorage =  new \common\CookieStorage();
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
    public function savePersistentBasket($hash){
        assert(is_string($hash));
        $this->persistentBasketDAL->save($hash);
    }

    //Save expiration dates for name and password on file
    public function saveExpirationDate($howLongWillUserBeRemembered){
        $this->persistentBasketDAL->saveExpiration($howLongWillUserBeRemembered, $howLongWillUserBeRemembered);
    }

    /**
     * Get name expiration from file
     * @return mixed
     */
    public function getNameExpiration(){
        return $this->persistentBasketDAL->loadNameExpiration();
    }

    /**
     * Get password expiration from file
     * @return mixed
     */
    public function getPasswordExpiration(){
        return $this->persistentBasketDAL->loadPasswordExpiration();
    }

    /**
     * Get temporary password from file
     * @return string
     */
    public function getStoredPassword(){
        return $this->persistentBasketDAL->load();
    }

    public function getHashedPassword(){
        return $this->hashedPassword;
    }
}