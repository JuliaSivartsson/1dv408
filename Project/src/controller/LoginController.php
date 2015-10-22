<?php

namespace controller;


use common\Messages;
use model\UserModel;

class LoginController{

    private $loginModel;
    private $loginView;
    private $hasLoggedIn = false;
    private $registrationView;
    private $navView;
    private $productRepository;
    private $persistentBasketDAL;
    private $productView;
    private $defaultView;
    private $userModel;


    public function __construct(){
        $this->loginModel = new \model\LoginModel();
        $this->sessionStorage = new \common\SessionStorage();
        $this->productRepository = new \model\dal\ProductRepository();
        $this->persistentBasketDAL = new \model\dal\ProductBasketDAL();

        $this->navView = new \view\NavigationView();
        $this->registrationView = new \view\Registerview();
        $this->productView = new \view\ProductView($this->productRepository, $this->navView);
        $this->loginView = new \view\LoginView($this->loginModel);
        $this->defaultView = new \view\DefaultView();
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
            $this->loginView->reloadFailPage();
        }catch(\model\PasswordTooShort $e){
            $this->loginView->setFlashMessage(Messages::$passwordIsNotCorrect);
            $this->loginView->reloadFailPage();
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
            $this->loginView->reloadFailPage();
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