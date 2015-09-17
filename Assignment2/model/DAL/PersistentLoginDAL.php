<?php

namespace model;

//Class for saving cookies on file
class PersistentLoginDAL
{
    private static $persistentLoginFile = "persistentLogin.txt";
    private static $expirationTimeFile = "expirationTimes.txt";

    /**
     * Write to file
     * @param $token
     */
    public function save($token){
        $handleFile = fopen(self::$persistentLoginFile, 'w') or die('The file could not be opened. Please try again.');
        fwrite($handleFile, $token);
        fclose($handleFile);
    }

    /**
     * Read from file
     * @return string
     */
    public function load(){
        $handleFile = fopen(self::$persistentLoginFile, 'r');
        $token = fread($handleFile, filesize(self::$persistentLoginFile));
        fclose($handleFile);
        return $token;
    }

    /**
     * @param $nameExpiration
     * @param $passwordExpiration
     */
    public function saveExpiration($nameExpiration, $passwordExpiration){
        $handleFile = fopen(self::$expirationTimeFile, 'w') or die('The file could not be opened. Please try again');
        fwrite($handleFile, $nameExpiration . "\n");
        fwrite($handleFile, $passwordExpiration);
        fclose($handleFile);
    }

    /**
     * @return mixed
     */
    public function loadNameExpiration(){
        $readFile = file(self::$expirationTimeFile);
        return $readFile[0];
    }

    /**
     * @return mixed
     */
    public function loadPasswordExpiration(){
        $readFile = file(self::$expirationTimeFile);
        return $readFile[1];
    }


}