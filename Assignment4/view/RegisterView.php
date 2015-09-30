<?php

namespace view;

use \common\Messages;

class RegisterView implements IView {

    private static $username = "RegisterView::UserName";
    private static $password = "RegisterView::Password";
    private static $passwordRepeat = "RegisterView::PasswordRepeat";
    private static $registration = "RegisterView::Register";
    private static $messageId = 'RegisterView::Message';

    private $message;

    /**
     * Create HTTP response
     *
     * Should be called after a login attempt has been determined
     *
     * @return  void BUT writes to standard output and cookies!
     */
    public function response() {
        $message = $this->message;

        return $this->generateRegistrationFormHTML($message);
    }

    private function generateRegistrationFormHTML($message){

    return "
          <h2>Register new user</h2>
    			<form method='post' >
            <fieldset>
            <legend>Register a new user - Write username and password</legend>
              <p id='" . self::$messageId . "'>" . $message ."</p>
              <label for='" . self::$username . "' >Username :</label>
              <input type='text' size='20' name='" . self::$username . "' id='" . self::$username . "' value='" . strip_tags($this->getRegisterUsername())  ."' />
              <br/>
              <label for='" . self::$password . "' >Password  :</label>
              <input type='password' size='20' name='" . self::$password . "' id='" . self::$password . "' value='' />
              <br/>
              <label for='" . self::$passwordRepeat . "' >Repeat password  :</label>
              <input type='password' size='20' name='" . self::$passwordRepeat . "' id='" . self::$passwordRepeat . "' value='' />
              <br/>
              <input id='submit' type='submit' name='" . self::$registration . "'  value='Register' />
              <br/>
            </fieldset>
    			</form>

    		";
    }

    public function registerAttempt() {
        return isset($_POST[self::$registration]);
    }

    public function getRegisterUsername(){
        if(isset($_POST[self::$username])) {
            return $_POST[self::$username];
        }
        return null;
    }

    public function getRegisterPassword(){
        if(isset($_POST[self::$password])) {
            return $_POST[self::$password];
        }
    }

    public function getRegisterPasswordRepeat(){
        if(isset($_POST[self::$passwordRepeat])) {
            return $_POST[self::$passwordRepeat];
        }
    }

    public function getRegistrationInfo()
    {
        $message = "";
        $canIRegisterNewUser = true;

        if(strlen($this->getRegisterUsername()) < 3){

            $message .= Messages::$usernameTooShort . "<br>";
            $canIRegisterNewUser = false;
        }
        if(strlen($this->getRegisterPassword()) < 6){
            $message .= Messages::$passwordTooShort;
            $canIRegisterNewUser = false;
        }
        if($this->getRegisterPassword() !== $_POST[self::$passwordRepeat]){
            $message .= Messages::$passwordIsNotSame;
            $canIRegisterNewUser = false;
        }
        if($this->getRegisterUsername() !== strip_tags($this->getRegisterUsername())){
            $message .= Messages::$forbiddenCharacters;
            $canIRegisterNewUser = false;
        }

        $this->message = $message;

        if($canIRegisterNewUser){
            return new \model\UserModel($this->getRegisterUsername(), $this->getRegisterPassword());
        }
        else{
            return null;
        }
    }

    public function userJustRegistered(bool $didUserJustRegister){
        return $didUserJustRegister;
    }

    public function reloadPage(){
        header('Location: /Assignment4/index.php' );
    }


    //Set message to show user
    public function setMessage($message){
        assert(is_string($message));
        return $this->message = $message;
    }
}