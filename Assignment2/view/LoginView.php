<?php

namespace view;

use \common\Messages;

class LoginView {

	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';

    private $expirationDate;
	private $model;
	private $message;

	public function __construct(\model\LoginModel $loginModel){
		$this->model = $loginModel;
        $this->cookie = new \common\CookieStorage();
	}

	/**
	 * Create HTTP response
	 *
	 * Should be called after a login attempt has been determined
	 *
	 * @return  void BUT writes to standard output and cookies!
	 */
	public function response($isLoggedIn, $hasLoggedIn) {

        $message = $this->message;
        $messageID = "view::LoginView::message";

        //Reload page after login to remove POST call
        if($this->loginAttempt() && $isLoggedIn == TRUE && $hasLoggedIn == TRUE){

            if($this->userWantsToBeRemembered()){
                $message = Messages::$keepUserSignedIn;
            }else{
                $message = Messages::$login;
            }

            $_SESSION[$messageID] = $message;
            $this->reloadPage();

        }
        else if(isset($_SESSION[$messageID])){

            $message =  $_SESSION[$messageID];

            unset($_SESSION[$messageID]);
        }

        if($isLoggedIn){
            $response = $this->generateLogoutButtonHTML($message);
        }
        else{
            $response = $this->generateLoginFormHTML($message);
        }

		return $response;
	}

	/**
	 * @param $message
	 * @return string
	 */
	private function generateLogoutButtonHTML($message) {

		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}

	/**
	 * @param $message
	 * @return string
	 */
	private function generateLoginFormHTML($message) {

        return '
			<form method="post" > 
				<fieldset>
					<legend>Login - enter Username and password</legend>

					<p id="' . self::$messageId . '">' . $message . '</p>
					
					<label for="' . self::$name . '">Username :</label>
					<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getRequestUserName()  .'" />
					<label for="' . self::$password . '">Password :</label>
					<input type="password" id="' . self::$password . '" name="' . self::$password . '" />
					<label for="' . self::$keep . '">Keep me logged in  :</label>
					<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />
					
					<input type="submit" name="' . self::$login . '" value="login" />
				</fieldset>
			</form>
		';

	}

	//Get user input for username
	public function getRequestUserName() {
		if(isset($_POST[self::$name])) {
			return $_POST[self::$name];
		}
		return null;
	}

	//Get user input for password
	public function getRequestPassword() {
		if(isset($_POST[self::$password])) {
			return $_POST[self::$password];
		}
		return null;
	}

	public function usernameMissing(){
		return empty($_POST[self::$name]);
	}

	public function passwordMissing(){
		return empty($_POST[self::$password]);
	}

	//If user presses login-button
	public function loginAttempt(){
		return isset($_POST[self::$login]);
	}

	//If user presses logout-button
	public function logoutAttempt(){
		return isset($_POST[self::$logout]);
	}

	public function userWantsToBeRemembered(){
		return isset($_POST[self::$keep]);
	}

	/**
	 * Save cookie and remember user
	 * @return string
	 */
	public function rememberUser(){
		$hashedPassword = $this->model->getHashedPassword();
        $this->expirationDate = time() + (86400 * 30);

        //Save cookie for name and password
        $this->cookie->save(self::$cookieName, $this->getRequestUserName(), $this->expirationDate);
        $this->cookie->save(self::$cookiePassword, $hashedPassword, $this->expirationDate);

		return $hashedPassword;
	}

    //Return true if cookie or session exists
    public function isLoggedIn(){
        return $this->isCookieSet() || $this->model->isUserSaved();
    }

    public function getExpirationDate(){
        return $this->expirationDate;
    }

    //Delete cookie
    public function forgetUser(){
        $this->cookie->delete(self::$cookieName);
        $this->cookie->delete(self::$cookiePassword);
    }

    /**
     * Returns true if session doesn't exist but cookie does
     * @return bool
     */
    public function isUserComingBack(){
        return $this->model->isUserSaved() === false && $this->isCookieSet();
    }

	//Check if both cookies exists
    public function isCookieSet(){
        return $this->cookie->load(self::$cookieName) && $this->cookie->load(self::$cookiePassword);
    }

	/**
	 * If time is larger than expirationdate, a cookie is removed.
	 * This method checks to see if there is only one cookie when there should be two.
	 * @return bool
	 */
	public function doesOneCookieExists(){
		return $this->cookie->load(self::$cookieName) || $this->cookie->load(self::$cookiePassword);
	}

	/**
	 * Did user change state of existing cookie
	 * @return bool
	 */
	public function didUserChangeCookie(){
		if($this->isCookieSet()){
			//Check if credentials matches cookie on file
            return $this->model->getUsername() !== $this->cookie->load(self::$cookieName) ||
                $this->model->getStoredPassword() !== $this->cookie->load(self::$cookiePassword) ||
                time() > $this->model->getNameExpiration() ||
                time() > $this->model->getPasswordExpiration();
		}
        else if($this->doesOneCookieExists()){
            return true;
        }
		else{
			return false;
		}
	}


	public function getUserIdentifier(){
		return $_SERVER['HTTP_USER_AGENT'];
	}

	public function reloadPage(){
		header('Location: /' );
	}

    //Set message to show user
	public function setMessage($message){
        assert(is_string($message));
		return $this->message = $message;
	}

}