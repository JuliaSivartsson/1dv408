<?php

namespace controller;


use common\Messages;
use common\SessionStorage;
use model\dal\ProductBasketDAL;
use model\LoginModel;
use model\UserModel;
use view\DefaultView;
use view\LoginView;
use view\NavigationView;
use view\ProductView;
use view\RegisterView;

class LoginController{

    private $loginModel;
    private $loginView;
    private $hasLoggedIn = false;
    private $registrationView;
    private $navView;
    private $persistentBasketDAL;
    private $productView;
    private $defaultView;
    private $userModel;


    public function __construct(){
        $this->loginModel = new LoginModel();
        $this->sessionStorage = new SessionStorage();
        $this->persistentBasketDAL = new ProductBasketDAL();

        $this->navView = new NavigationView();
        $this->registrationView = new RegisterView();
        $this->productView = new ProductView($this->navView);
        $this->loginView = new LoginView($this->loginModel);
        $this->defaultView = new DefaultView();
    }

    /**
     * Make sure user has not manipulated cookies
     * @return bool
     */
    public function isUserOkay(){
        if($this->loginView->didUserChangeCookie()){
            $this->doLogout();
            $this->productView->setMessage(Messages::$notOkayUser);
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

        try{
            $this->userModel = new UserModel($username, $password);
        }catch(\model\UsernameTooShortException $e){
            $this->loginView->setFlashMessage(Messages::$usernameIsNotCorrect);
            $this->loginView->reloadLoginPage();
        }catch(\model\PasswordTooShort $e){
            $this->loginView->setFlashMessage(Messages::$passwordIsNotCorrect);
            $this->loginView->reloadLoginPage();
        }

        $user = $this->userModel->getUserByUsername($username);
        if($user !== null && $user->authenticateLogin($username, $password)) {

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
            $this->loginView->reloadLoginPage();
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