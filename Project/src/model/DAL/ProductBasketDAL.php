<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-02
 * Time: 21:35
 */

namespace model\dal;


class ProductBasketDAL
{
    private static $persistentBasketFile = "persistentBasket.txt";
    private static $expirationTimeFile = "expirationTimesForBasket.txt";

    /**
     * Write to file
     * @param $token
     */
    public function save($token){
        $handleFile = fopen(self::$persistentBasketFile, 'a') or die('The file could not be opened. Please try again.');
        fwrite($handleFile, $token. "\n");
        fclose($handleFile);
    }

    /**
     * Read from file
     * @return string
     */
    public function load(){
        if(filesize(self::$persistentBasketFile) === 0){
            return false;
        }
        else {
            $handleFile = fopen(self::$persistentBasketFile, 'r');
            $token = fread($handleFile, filesize(self::$persistentBasketFile));
            fclose($handleFile);
            return $token;
        }
    }

    /**
     * @param $nameExpiration
     * @param $passwordExpiration
     */
    public function saveExpiration($nameExpiration, $passwordExpiration){
        $handleFile = fopen(self::$expirationTimeFile, 'a') or die('The file could not be opened. Please try again');
        fwrite($handleFile, $nameExpiration . "\n");
        fwrite($handleFile, $passwordExpiration);
        fclose($handleFile);
    }

    public function loadNameExpiration(){
        $readFile = file(self::$expirationTimeFile);
        return $readFile[0];
    }

    public function loadPasswordExpiration(){
        $readFile = file(self::$expirationTimeFile);
        return $readFile[1];
    }

    public function clearBasket(){
        file_put_contents(self::$persistentBasketFile, '');
    }

}