<?php

namespace model\dal;

class DatabaseConnection {

    private static $Dsn = \Settings::DSN;
    private static $Database = \Settings::DATABASE;
    private static $User = \Settings::DB_USERNAME;
    private static $Password = \Settings::DB_PASSWORD;

    public function SetupDatabase() {
        return new \PDO('mysql:host=' . self::$Dsn . ';dbname=' . self::$Database . ';', self::$User, self::$Password, array(\PDO::FETCH_OBJ));
    }
}