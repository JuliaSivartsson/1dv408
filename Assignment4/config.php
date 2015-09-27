<?php

//MAKE SURE ERRORS ARE SHOWN... MIGHT WANT TO TURN THIS OFF ON A PUBLIC SERVER
error_reporting(E_ALL);
ini_set('display_errors', 'On');

//Include the needed files
require_once('view/IView.php');
require_once('view/LoginView.php');
require_once('view/LayoutView.php');
require_once('view/RegisterView.php');

require_once('controller/LoginController.php');
require_once('controller/RegistrationController.php');

require_once('model/LoginModel.php');
require_once('model/UserModel.php');

require_once('common/Messages.php');
require_once('common/SessionStorage.php');
require_once('common/CookieStorage.php');

