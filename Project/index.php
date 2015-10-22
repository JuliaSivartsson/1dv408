<?php

//Include needed file
require_once('config.php');

$controller = new \controller\MasterController();
$lv = new \view\DefaultView();
$m = new \model\LoginModel();
$v = new \view\LoginView($m);

$lv->getHTML($v->isLoggedIn(), $controller->main());
