<?php
namespace model;

abstract class DBBase {
// Init variables
    private static $DB_SETTINGS = [
        "type" => "mysql",
        "name" => "assignment4-local",
        "username" => "root",
        "password" => "",
        "charset" => "utf8",
        "host" => "localhost"
    ];
    private static $DB_CONNECT_ERROR = 'Error connecting to database. Please contact page admin.';
    protected static $db;
// Constructor
    public function __construct() {
        // Setup pdo
        $this->SetupDB();
    }
// Getters and Setters
// Private methods
    private static function SetupDB() {
        try {
            // Setup DSN string
            $dsn = self::$DB_SETTINGS['type'] . ':dbname=' . self::$DB_SETTINGS['name'] . ';host=' . self::$DB_SETTINGS['host'];
            // Connect to an ODBC database using driver invocation
            self::$db = new \PDO(
                $dsn,
                self::$DB_SETTINGS['username'],
                self::$DB_SETTINGS['password']
            );
            // Set error mode to silent
            self::$db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
        } catch (\Exception $exception) {
            throw new \PDOException(self::$DB_CONNECT_ERROR);
        }
    }
}