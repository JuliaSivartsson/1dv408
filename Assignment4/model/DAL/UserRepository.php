<?php
namespace model\dal;

use model\UserModel;

class UserRepository{

    private $database;

    public function __construct(){
        $connection = new DatabaseConnection();

        try{
            $this->database = $connection->SetupDatabase();
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function save(UserModel $user){
        $stmt = $this->database->prepare("INSERT INTO user (username, password) VALUE (?,?)");
        $stmt->execute(array($user->getUsername(), $user->getPassword()));
    }

    public function getAllUsers()
    {
        $ret = array();
        $stmt = $this->database->prepare("SELECT * FROM user ORDER BY username");
        $stmt->execute();
        while($user= $stmt->fetchObject()){
            $ret[] =  new \model\UserModel($user->username, $user->password);
        }
        return $ret;
    }

}