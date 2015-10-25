<?php

namespace controller;


use common\CookieStorage;
use common\Messages;

class RegistrationController{

    private $layoutView;
    private $registerView;
    private $cookieStorage;

    public function __construct(\view\RegisterView $registrationView, \view\DefaultView $layoutView){
        $this->registerView = $registrationView;
        $this->layoutView = $layoutView;
        $this->cookieStorage = new CookieStorage();

    }

    public function doRegister(){
        $username = $this->registerView->getRegisterUsername();

        $userToRegister = $this->registerView->getRegistrationInfo();

        if($userToRegister !== null){
            try{
                if($userToRegister->doesUserAlreadyExists($username)){
                    $this->registerView->setMessage(Messages::$userExists);
                }
                else {
                    $userToRegister->saveNewUser($userToRegister);
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