<?php
namespace model\dal;

use model\UserModel;

class UserRepository{

    private $database;
    private static $usernameColumn = 'username';
    private static $passwordColumn = 'password';

    public function __construct(){
        $this->dbTable = 'user';
        $connection = new DatabaseConnection();

        try{
            $this->database = $connection->SetupDatabase();
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function save(UserModel $user){
        try {
            $sql = "INSERT INTO $this->dbTable (" . self::$usernameColumn . "," . self::$passwordColumn . ") VALUE (?,?)";
            $params = array($user->getUsername(), sha1($user->getPassword()));
            $query = $this->database->prepare($sql);
            $query->execute($params);
        }catch(\PDOException $e){
            return false;
        }
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

    public function getUserByUsername($username){
        try {

            $sql = "SELECT * FROM $this->dbTable WHERE " . self::$usernameColumn . " = ?";
            $params = array($username);
            $query = $this->database->prepare($sql);
            $query->execute($params);
            $result = $query->fetch();
            if ($result) {
                $user = new \model\UserModel($result[self::$usernameColumn], $result[self::$passwordColumn]);
                return $user;
            }
            return null;
        } catch (\PDOException $e) {
            die("User not found");
        }
    }

}