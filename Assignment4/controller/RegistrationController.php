<?php

namespace controller;


use common\Messages;
use model\UserRepository;

class RegistrationController{

    private $layoutView;
    private $registerView;
    private $userModel;
    private $userRepository;
    private $database;

    public function __construct(\view\RegisterView $registrationView, \view\LayoutView $layoutView){
        $this->registerView = $registrationView;
        $this->layoutView = $layoutView;

        $this->database = new UserRepository();

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

        $userToRegister = $this->registerView->getRegistrationInfo();


        if($userToRegister !== null){
            try{

                foreach($this->database->GetAll() as $users){
                    echo $users->getUsername();
                }

                //save user to database
                if($this->database->addUser($userToRegister) === 1){

                    die('banan');
                    //$this->registerView->setMessage(Messages::$successfulRegistration);
                }
            }catch (\Exception $e){
                $this->registerView->setMessage($e->getMessage());
            }

        }
        else{

        }
    }

}