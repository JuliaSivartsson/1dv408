<?php

namespace controller;


use common\Messages;

class RegistrationController{

    private $layoutView;
    private $registerView;

    public function __construct(\view\RegisterView $registrationView, \view\LayoutView $layoutView){
        $this->registerView = $registrationView;
        $this->layoutView = $layoutView;
    }

    //Call HTML-code to be rendered
    public function doRender(){

        if($this->registerView->registerAttempt()){
            $this->doRegister();
        }

        $this->layoutView->getHTML(false, $this->registerView, false);
    }

    public function doRegister(){
        $this->registerView->getRegisterPassword();
        $this->registerView->getRegisterUsername();

        $this->registerView->getRegistrationInfo();
    }

}