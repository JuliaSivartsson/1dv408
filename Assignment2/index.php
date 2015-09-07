<?php

//INCLUDE THE FILES NEEDED...
require_once('view/LoginView.php');
require_once('view/DateTimeView.php');
require_once('view/LayoutView.php');

//Controller
require_once('controller/LoginController.php');

//Model
//require_once('model/UserModel.php');
require_once('model/LoginModel.php');
require_once('model/SessionStoragePersistor.php');

error_reporting(E_ALL);
ini_set('display_errors', 'On');

//Session storage
//session_start();
//$sessionStorage = new \model\SessionStoragePersistor("LoginSessionLocation");



//CREATE OBJECTS OF THE VIEWS
/*$v = new LoginView();
$dtv = new DateTimeView();
$lv = new LayoutView();*/

$controller = new \controller\LoginController();
$controller->render();

//$controller->render();

//$htmlView = new \view\LayoutView();
//echo $htmlView->render($thingsToOutput);

//$lv->render(false, $v, $dtv);

