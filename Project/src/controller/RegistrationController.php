<?php

namespace controller;


use common\Messages;
use model\dal\UserRepository;

class RegistrationController{

    private $layoutView;
    private $registerView;
    private $userModel;
    private $database;
    private $cookieStorage;

    public function __construct(\view\RegisterView $registrationView, \view\DefaultView $layoutView){
        $this->registerView = $registrationView;
        $this->layoutView = $layoutView;

        $this->database = new UserRepository();
        $this->cookieStorage = new \common\CookieStorage();

    }

    //Call HTML-code to be rendered
    public function doRegister(){

        $password = $this->registerView->getRegisterPassword();
        $username = $this->registerView->getRegisterUsername();


        $this->userModel = new \model\UserModel($username, $password);
        $userToRegister = $this->registerView->getRegistrationInfo();


        if($userToRegister !== null){
            try{

                if($this->userModel->doesUserAlreadyExists($username)){
                    $this->registerView->setMessage(Messages::$userExists);
                }
                else {
                    $this->userModel->saveNewUser($userToRegister);
                    $this->cookieStorage->save(\view\LoginView::SAVED_USERNAME, $userToRegister->getUserName(), time() + 3600);
                    return true;
                }

            }catch (\Exception $e){
                $this->registerView->setMessage($e->getMessage());
            }

        }
        return false;
    }
}