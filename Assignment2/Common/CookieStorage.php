<?php

namespace common;

class CookieStorage{

    //Save new cookie
    public function save($cookieName, $value, $expirationDate){
        setcookie($cookieName, $value, $expirationDate);
        $_COOKIE[$cookieName] = $value;
    }

    //Load cookie if it exists
    public function load($cookieName){
        if( isset($_COOKIE[$cookieName])){
            return $_COOKIE[$cookieName];
        }else{
            return false;
        }
    }

    //Delete a cookie
    public function delete($cookieName){
        unset($_COOKIE[$cookieName]);
        setcookie($cookieName, "", time() - 3600);
    }
}