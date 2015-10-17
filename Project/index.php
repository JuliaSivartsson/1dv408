<?php

//Include needed file
require_once('config.php');

//Create object of the controller
$controller = new \controller\ProductController();
$lv = new \view\DefaultView();
//$controller->Main();
$m = new \model\LoginModel();
$v = new \view\LoginView($m);


//$m = new \model\LoginModel();
//$v = new \view\LoginView($m);
//$c = new \controller\ProductController();
//$c->render();
//$lv = new \view\DefaultView();
$lv->getHTML($v->isLoggedIn(), $controller->main());

//$this->defaultView->getHTML($this->loginView->isLoggedIn(), $this->productController->Main());