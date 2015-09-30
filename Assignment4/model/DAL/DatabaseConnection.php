<?php

namespace model\dal;

class DatabaseConnection {

    private static $Dsn = 'localhost';
    private static $Database = 'assignment4';
    private static $User = 'root';
    private static $Password = '';

    public function SetupDatabase() {
        return new \PDO('mysql:host=' . self::$Dsn . ';dbname=' . self::$Database . ';', self::$User, self::$Password, array(\PDO::FETCH_OBJ));
    }
}