<?php

namespace common;


//Class for managing cookies
class CookieStorage{

    //Save new cookie
    public function save($cookieName, $value, $expirationDate){
        setcookie($cookieName, $value, $expirationDate);
        $_COOKIE[$cookieName] = $value;
    }

    public function isCookieSet($cookieName){
        return isset($_COOKIE[$cookieName]);
    }

    //Load cookie if it exists
    public function load($cookieName){
        if( isset($_COOKIE[$cookieName])){
            return $_COOKIE[$cookieName];
        }else{
            return false;
        }
    }

    public function delete($cookieName){
        unset($_COOKIE[$cookieName]);
        setcookie($cookieName, "", time() - 3600);
    }
}