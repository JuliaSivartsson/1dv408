<?php

namespace controller;


use common\Messages;

class RegistrationController{

    private $layoutView;
    private $registerView;
    private $registerModel;

    public function __construct(\view\RegisterView $registrationView, \view\LayoutView $layoutView){
        $this->registerView = $registrationView;
        $this->layoutView = $layoutView;

        $this->registerModel = new \model\UserModel();
    }

    //Call HTML-code to be rendered
    public function doRender(){

        if($this->registerView->registerAttempt()){
            $this->doRegister();
        }

        $this->layoutView->render(false, $this->registerView);
    }

    public function doRegister(){
        $password = $this->registerView->getRegisterPassword();
        $username = $this->registerView->getRegisterUsername();

        if($this->registerView->getRegistrationInfo() == 1){
            //check if passwords are equal
            if(strcmp($this->registerView->getRegisterPassword(), $this->registerView->getRegisterRepeatPassword()) === 0){

                //check if user already exists
                if($this->registerModel->doesUserAlreadyExists($username)){

                }
            }
            else{
                $this->registerView->setMessage(Messages::$passwordIsNotSame);
            }
        }
        else{

        }
    }

}