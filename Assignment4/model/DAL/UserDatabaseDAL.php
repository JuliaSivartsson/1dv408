<?php

namespace model;

//Class for saving cookies on file
class UserDatabaseDAL
{

    private static $table = "assignment4-local";
    private $dbtable;
    private $mysqli;

    /*public function __construct()
    {
        $this->mysqli = mysqli_connect("localhost", "root", "", "assignment4-local");

        if (mysqli_connect_errno()) {
            printf("Connect failed: %s\n", mysqli_connect_error());
            exit();
        }
        $this->dbtable = "users";
    }

    public function addUser($userToAdd)
    {
        $query = "INSERT INTO `assignment4-local`.`user` (`ID`, `Username`, `Password`) VALUES (NULL, 'Admin', 'Password')";
    }


    public function readAllUsernames($username)
    {

    }

    public function getAllUsers()
    {

        try{
            $result = $this->mysqli->query("SELECT * FROM Users");
            die($result->num_rows);
        }catch(\Exception $e){
            echo $e->getMessage();
        }
        $this->mysqli->query("SELECT * FROM Users");
        if ($result = $this->mysqli->query("SELECT * FROM Users")) {
            return "Select returned %d rows.\n". $result->num_rows;

        }



    }*/
}