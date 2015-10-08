<?php

//Include needed file
require_once('config.php');

$m = new \model\LoginModel();
$v = new \view\LoginView($m);
$c = new \controller\LoginController($m, $v);

$c->render();
$lv = new \view\LayoutView();
$lv->render($v->isLoggedIn(), $v);