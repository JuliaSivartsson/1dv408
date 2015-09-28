<?php
namespace model;

class UserRepository extends DBBase {


// Init variables
    private static $DB_TABLE_NAME = 'user';
    private static $DB_QUERY_ERROR = 'Error getting users from database';
    private static $DB_GET_ERROR = 'Error getting user from database';
    private static $DB_INSERT_ERROR = 'Error adding user to database';
    private static $DB_UPDATE_ERROR = 'Error updating user in database';
// Constructor
// Public Methods
    public function GetAll()
    {
        try {
            $returnArray = [];
            // Get data from database
            foreach (self::$db->query('SELECT `Username`, `Password` FROM `' . self::$DB_TABLE_NAME . '`') as $row) {
                // Create new user object from database row
                $usersArray[] = new UserModel($row['Username'], $row['Password']);
            }
            return $usersArray;
        } catch (\Exception $exception) {
            throw new \Exception(self::$DB_QUERY_ERROR);
        }
    }


    public function addUser(\model\UserModel $user)
    {
        try {
            // Prepare db statement
            $statement = self::$db->prepare(
                'INSERT INTO ' . self::$DB_TABLE_NAME .
                '(Username, Password)' .
                ' VALUES ' .
                '(NULL, :userName, :password)'
            );

            // Prepare input array
            $inputArray = [
                'userName' => $user->getUsername(),
                'password' => $user->getPassword()
            ];
            // Execute db statement
            $statement->execute($inputArray);

            // Check if db insertion was successful
            return $statement->rowCount() == 1;
        } catch (\Exception $exception) {
            throw new \Exception(self::$DB_INSERT_ERROR);
        }
    }
}