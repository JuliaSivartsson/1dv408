<?php

namespace view;

use \common\Messages;

class RegisterView implements IView {

    private static $username = 'RegisterView::UserName';
    private static $password = 'RegisterView::Password';
    private static $repeatPassword = 'RegisterView::RepeatPassword';
    private static $registration = 'RegisterView::Registration';
    private static $messagePosition = 'RegisterView::Message';

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
              <p id='" . self::$messagePosition . "'>" . $message ."</p>
              <label for='" . self::$username . "' >Username :</label>
              <input type='text' size='20' name='" . self::$username . "' id='" . self::$username . "' value='' />
              <br/>
              <label for='" . self::$password . "' >Password  :</label>
              <input type='password' size='20' name='" . self::$password . "' id='" . self::$password . "' value='' />
              <br/>
              <label for='" . self::$repeatPassword . "' >Repeat password  :</label>
              <input type='password' size='20' name='" . self::$repeatPassword . "' id='" . self::$repeatPassword . "' value='' />
              <br/>
              <input id='submit' type='submit' name='" . self::$registration . "'  value='Register' />
              <br/>
            </fieldset>
    			</form>

    		";
    }

    public function registerAttempt(){
        return isset($_POST[self::$registration]);
    }

    public function getRegisterUsername(){
        assert($this->registerAttempt());
        return $_POST[self::$username];
    }

    public function getRegisterPassword(){
        assert($this->registerAttempt());
        return $_POST[self::$password];
    }

    public function getRegistrationInfo()
    {
        $message = "";

        if(strlen($this->getRegisterUsername()) < 3){
            $message .= \common\Messages::$usernameTooShort . "<br>";
        }
        if(strlen($this->getRegisterPassword()) < 6){
            $message .= \common\Messages::$passwordTooShort;
        }

        $this->message = $message;
    }


    //Set message to show user
    public function setMessage($message){
        assert(is_string($message));
        return $this->message = $message;
    }
}