<?php

namespace controller;

class LoginController{

    private $loginModel;
    private $loginView;
    private $dateTimeView;
    private $layoutView;

    public function __construct(){

        $this->loginModel = new \model\LoginModel();

        $this->loginView = new \view\LoginView($this->loginModel);
        $this->dateTimeView = new \view\DateTimeView();
        $this->layoutView = new \view\LayoutView();
    }


    //Call HTML-code to be rendered
    public function render(){

        //Get info from view
        $username = $this->loginView->getRequestUserName();
        $password = $this->loginView->getRequestPassword();

        //If user tries to login and is not logged in
        if($this->loginView->loginAttempt() && $this->checkUserStatus() == FALSE){
            $this->doLogin($username, $password);
        }

        //If user tries to logout and is logged in
        if($this->loginView->logoutAttempt()&& $this->checkUserStatus() == TRUE){
            $this->doLogout();
        }

        //Render HTML
        $this->layoutView->getHTML($this->checkUserStatus(), $this->loginView);
    }

    //Check if user is logged in or not
    public function checkUserStatus(){
        $isUserLoggedIn = $this->loginModel->isSessionSet();
        if($isUserLoggedIn == TRUE){
            return true;
        }
        else{
            return false;
        }
    }

    //Login user
    public function doLogin($username, $password){

        //If username is empty
        if($this->loginView->usernameMissing()){
            $this->loginView->setMessage(\common\Messages::$usernameEmpty);
            return;
        }
        //If password is empty
        else if($this->loginView->passwordMissing()){
            $this->loginView->setMessage(\common\Messages::$passwordEmpty);
            return;
        }
        //If credentials is correct
        if($this->loginModel->authenticate($username, $password) == TRUE){
            $this->loginView->setMessage(\common\Messages::$login);
            $this->loginModel->setSession($username);
        }
        else{
            $this->loginView->setMessage(\common\Messages::$wrongCredentials);
        }
    }

    //Logout user
    public function doLogout(){
        $this->loginView->setMessage(\common\Messages::$logout);
        $this->loginModel->unsetSession();
    }

}