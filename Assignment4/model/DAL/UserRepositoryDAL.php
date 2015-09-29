<?php

namespace model;

//Class for saving cookies on file
class UserRepositoryDAL
{

    private $firstWords = array();

    private static $userRepositoryFile = "userRepository.txt";

    /**
     * Write to file
     * @param $token
     */
    public function save($username, $password)
    {
        $handleFile = fopen(self::$userRepositoryFile, 'a') or die('The file could not be opened. Please try again.');
        fwrite($handleFile, $username . ",");
        fwrite($handleFile, $password . "\n");
        fclose($handleFile);
    }

    /**
     * Read from file
     * @return string
     */
    /*public function load()
    {
        $handleFile = fopen(self::$persistentLoginFile, 'r');
        $token = fread($handleFile, filesize(self::$persistentLoginFile));
        fclose($handleFile);
        return $token;
    }*/

    public function readAllUsernames(){
    // Open the file
            $fp = file(self::$userRepositoryFile);
        foreach ($fp as $val) {
            if (trim($val) != '') { //ignore empty lines
                $expl = explode(",", $val);
                $firstWords[] = $expl[0]; //add first word to the stack/array
            }
        }

        return $firstWords; //return the stack of words - thx furas ;D
    // Add each line to an array
            /*if ($fp) {
                $array = explode(",", fread($fp, filesize(self::$userRepositoryFile)));
            }*/
    }

}