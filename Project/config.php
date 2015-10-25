<?php

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//Include the needed files
require_once('Settings.php');
/*require_once('src/model/dal/DatabaseConnection.php');
require_once('src/model/dal/ProductRepository.php');
require_once('src/model/dal/CustomerRepository.php');
require_once('src/model/dal/OrderItemRepository.php');
require_once('src/model/dal/OrderRepository.php');
require_once('src/model/dal/PersistentLoginDAL.php');
require_once('src/model/dal/ProductBasketDAL.php');
require_once('src/model/dal/UserRepository.php');
*/

spl_autoload_register(function ($class) {
    $class = str_replace("\\", DIRECTORY_SEPARATOR, $class);
    $filename = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $class . '.php';
    if(file_exists($filename)){
        require_once $filename;
    }
});
