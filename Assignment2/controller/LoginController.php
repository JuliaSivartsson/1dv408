<?php

namespace controller;

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

        $layoutView = new \view\LayoutView();

        $layoutView->getHTML($isLoggedIn, $this->loginView);
    }

    //Check if user is logged in or not
    public function checkUserStatus(){
        return $this->loginModel->isSessionSet();
    }

    //
    public function doLogin($username, $password){

        //if username is empty
        if($this->loginView->usernameMissing()){
            $this->loginView->setMessage(\common\Messages::$usernameEmpty);
            return;
        }
        else if($this->loginView->passwordMissing()){
            $this->loginView->setMessage(\common\Messages::$passwordEmpty);
            return;
        }

        if($this->loginModel->authenticate($username, $password) == TRUE){
            $this->loginView->setMessage(\common\Messages::$login);
        }
        else{
            $this->loginView->setMessage(\common\Messages::$wrongCredentials);
        }
    }

    public function doLogout(){

    }

}