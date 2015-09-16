<?php

namespace controller;

class LoginController{

    private $loginModel;
    private $loginView;
    private $layoutView;

    public function __construct(){

        $this->loginModel = new \model\LoginModel();
        $this->loginView = new \view\LoginView($this->loginModel);
        $this->layoutView = new \view\LayoutView();
        $this->sessionStorage = new \common\SessionStorage();
    }

    //Call HTML-code to be rendered
    public function render(){
        if($this->isUserOkay()) {

            //If user tries to login and is not logged in
            if ($this->loginView->loginAttempt() && $this->loginView->isLoggedIn() == FALSE) {
                $this->doLogin();
            }

            //If user tries to logout and is logged in
            if ($this->loginView->logoutAttempt() && $this->loginView->isLoggedIn() == TRUE) {
                $this->doLogout();
                $this->loginView->setMessage(\common\Messages::$logout);
            }

            //If user is coming back with cookie
            if ($this->loginView->isUserComingBack()) {
                $this->loginView->setMessage(\common\Messages::$userReturning);
            }
        }
            //Render HTML
            $this->layoutView->getHTML($this->loginView->isLoggedIn(), $this->loginView);
    }

    //Make sure user has not manipulated cookies
    public function isUserOkay(){
        if($this->loginView->didUserChangeCookie()){
            $this->doLogout();
            $this->loginView->reloadPage();
            $_SESSION['woop'] = $this->loginView->setMessage(\common\Messages::$notOkayUser);
            return false;
        }
        else
        {
            return true;
        }
    }

    //Login user
    public function doLogin(){

        if(isset($_SESSION['woop'])){
            unset($_SESSION['woop']);
        }
        //Get info from view
        $username = $this->loginView->getRequestUserName();
        $password = $this->loginView->getRequestPassword();

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
        //If credentials are correct
        if($this->loginModel->authenticate($username, $password)) {
            $this->loginView->setMessage(\common\Messages::$login);
            //Login user in model
            $this->loginModel->login($username, $password);

            //If user wants to be remembered
            if ($this->loginView->userWantsToBeRemembered()) {

                //Get hashed password and expirationdate
                $passwordToIdentifyUser = $this->loginView->rememberUser();
                $howLongWillUserBeRemembered = $this->loginView->getExpirationDate();

                //Set cookie
                $this->loginModel->saveExpirationDate($howLongWillUserBeRemembered);
                $this->loginModel->savePersistentLogin($passwordToIdentifyUser);
                $this->loginView->setMessage(\common\Messages::$keepUserSignedIn);
            }
        }
        else{
            $this->loginView->setMessage(\common\Messages::$wrongCredentials);
        }
    }

    //Logout user
    public function doLogout(){
        $this->loginView->forgetUser();
        $this->loginModel->logout();
    }

}