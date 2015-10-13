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
    private $productController;
    private $navView;
    private $productRepository;
    private $persistentBasketDAL;
    private $productView;
    private $defaultView;


    public function __construct(){

        $this->loginModel = new \model\LoginModel();
        $this->loginView = new \view\LoginView($this->loginModel);
        $this->layoutView = new \view\LayoutView();
        $this->registrationView = new \view\Registerview();
        $this->sessionStorage = new \common\SessionStorage();
        $this->userRepository = new \model\dal\UserRepository();

        $this->productController = new \controller\ProductController();
        $this->navView = new \view\NavigationView();
        $this->productRepository = new \model\dal\ProductRepository();
        $this->persistentBasketDAL = new \model\dal\ProductBasketDAL();
        $this->productView = new \view\ProductView($this->productRepository, $this->navView);
        $this->defaultView = new \view\DefaultView();
    }

    //Call HTML-code to be rendered
    public function render(){
       /* $this->loginView->getFlashMessage();

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
                    if($this->doLogin()){
                        $this->productController->Main();
                    }
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

            if($this->loginView->isLoggedIn() === false) {
                $this->defaultView->getHTML($this->loginView->isLoggedIn(), $this->productController->Main());
            }
            else{
                $this->defaultView->getHTML($this->loginView->isLoggedIn(), $this->productController->Main());
            }
        }*/
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

            $this->loginView->setFlashMessage(Messages::$usernameEmpty);
            return;
        }
        else if($this->loginView->passwordMissing()){
            $this->loginView->setFlashMessage(Messages::$passwordEmpty);
            return;
        }
        //If credentials are correct
        if($this->userRepository->getUserByUsername($username) !== null && $user->authenticateLogin($username, $password)) {

            $this->loginView->setFlashMessage(Messages::$login);
            $this->loginModel->login($username, $password);
            if ($this->loginView->userWantsToBeRemembered()) {

                //Get hashed password and expirationdate
                $passwordToIdentifyUser = $this->loginView->rememberUser();
                $howLongWillUserBeRemembered = $this->loginView->getExpirationDate();

                //Set cookie
                $this->loginModel->saveExpirationDate($howLongWillUserBeRemembered);
                $this->loginModel->savePersistentLogin($passwordToIdentifyUser);
                $this->loginView->setFlashMessage(Messages::$keepUserSignedIn);
            }
            $this->hasLoggedIn = true;
            return true;
        }
        else{
            $this->loginView->setFlashMessage(Messages::$wrongCredentials);
        }
    }

    //Logout user
    public function doLogout(){
        $this->productView->forgetBasket();
        $this->persistentBasketDAL->clearBasket();
        $this->loginView->forgetUser();
        $this->loginModel->logout();
        $this->loginView->reloadPage();
    }

}