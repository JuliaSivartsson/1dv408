<?php
namespace controller;

use view\DateTimeView;
use view\LayoutView;

class LoginController{

    private $loginModel;
    private $loginView;

    private $dateTimeView;
    private $layoutView;
    private $isLoggedIn;

    public function __construct(){

        $this->loginModel = new \model\LoginModel();
        //$this->loginView = new \view\LoginView();

        $this->loginView = new \view\LoginView($this->loginModel);

        $this->dateTimeView = new \view\DateTimeView();
        $this->layoutView = new \view\LayoutView();
    }


    //call HTML from view
    public function render(){
        $username = $this->loginView->getRequestUserName();
        $password = $this->loginView->getRequestPassword();

        //If user tries to login
        if($this->loginView->loginAttempt()){
            $this->doLogin($username, $password);
        }

        //If user tries to logout
        if($this->loginView->logoutAttempt()){
            $this->doLogout();
        }

        if($this->checkUserStatus()){
            $isLoggedIn = true;
        }
        else{
            $isLoggedIn = false;
        }

        $layoutView = new LayoutView();

        $layoutView->getHTML($isLoggedIn, $this->loginView);
    }

    //Check if user is logged in or not
    public function checkUserStatus(){
        return $this->loginModel->isSessionSet();
    }

    public function logInAttempt(){

    }

    public function doLogin($username, $password){
        $result = $this->loginModel->authenticate($username, $password);
        $this->loginView->setMessage($result);
    }

    public function doLogout(){

    }

}