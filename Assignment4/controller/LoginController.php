<?php

namespace controller;


use common\Messages;

class LoginController{

    private $loginModel;
    private $loginView;
    private $layoutView;
    private $hasLoggedIn = false;
    private $registrationController;
    private $registrationView;

    public function __construct(){

        $this->loginModel = new \model\LoginModel();
        $this->loginView = new \view\LoginView($this->loginModel);
        $this->layoutView = new \view\LayoutView();
        $this->registrationView = new \view\Registerview();
        $this->sessionStorage = new \common\SessionStorage();
        $this->userRepository = new \model\dal\UserRepository();
    }

    //Call HTML-code to be rendered
    public function render(){

        $this->loginView->getFlashMessage();

        //If user pressed link to register
        if($this->layoutView->UserWantsToRegister()){
            $this->registrationController = new \controller\RegistrationController($this->registrationView, $this->layoutView);

            if($this->registrationController->doRegister()){
                $this->loginView->setFlashMessage(Messages::$successfulRegistration);
                $this->loginView->reloadPage();
            }
            else{
                return;
            }
        }
        else {
            if ($this->isUserOkay()) {

                //If user tries to login and is not logged in
                if ($this->loginView->loginAttempt() && $this->loginView->isLoggedIn() == FALSE) {
                    $this->doLogin();
                }

                //If user tries to logout and is logged in
                if ($this->loginView->logoutAttempt() && $this->loginView->isLoggedIn() == TRUE) {
                    $this->doLogout();
                    $this->loginView->setMessage(Messages::$logout);
                }

                //If user is coming back with cookie
                if ($this->loginView->isUserComingBack()) {
                    $this->loginView->setMessage(Messages::$userReturning);
                }

            }
            $this->layoutView->render($this->loginView->isLoggedIn(), $this->loginView);
        }
    }

    /**
     * Make sure user has not manipulated cookies
     * @return bool
     */
    public function isUserOkay(){
        if($this->loginView->didUserChangeCookie()){
            $this->doLogout();
            $this->loginView->setMessage(Messages::$notOkayUser);
            return false;
        } else if($this->loginModel->isUserTheRightUser($this->loginView->getUserIdentifier()) === false){
            $this->doLogout();
            return false;
        }
        else
        {
            return true;
        }
    }

    //Login user
    public function doLogin(){
        //Get info from view
        $username = $this->loginView->getRequestUserName();
        $password = $this->loginView->getRequestPassword();
        $user = $this->userRepository->getUserByUsername($username);

        if($this->loginView->usernameMissing()){
            $this->loginView->setMessage(Messages::$usernameEmpty);
            return;
        }
        else if($this->loginView->passwordMissing()){
            $this->loginView->setMessage(Messages::$passwordEmpty);
            return;
        }
        //If credentials are correct
        if($this->userRepository->getUserByUsername($username) !== null && $user->authenticateLogin($username, $password)) {
            $this->loginView->setMessage(Messages::$login);
            $this->loginModel->login($username, $password);
            if ($this->loginView->userWantsToBeRemembered()) {
                //Get hashed password and expirationdate
                $passwordToIdentifyUser = $this->loginView->rememberUser();
                $howLongWillUserBeRemembered = $this->loginView->getExpirationDate();
                //Set cookie
                $this->loginModel->saveExpirationDate($howLongWillUserBeRemembered);
                $this->loginModel->savePersistentLogin($passwordToIdentifyUser);
                $this->loginView->setMessage(Messages::$keepUserSignedIn);
            }
            $this->hasLoggedIn = true;
        }
        else{
            $this->loginView->setMessage(Messages::$wrongCredentials);
        }
    }

    //Logout user
    public function doLogout(){
        $this->loginView->forgetUser();
        $this->loginModel->logout();
    }

}