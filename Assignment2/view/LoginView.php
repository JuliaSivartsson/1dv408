<?php

namespace view;

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
    private $number_of_days = 10;

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
	public function response($isLoggedIn) {
		$message = $this->message;

        if($isLoggedIn){
            $response = $this->generateLogoutButtonHTML($message);
        }
        else{
            $response = $this->generateLoginFormHTML($message);
        }

		return $response;
	}

    //Generate logout button
	private function generateLogoutButtonHTML($message) {
		return '
			<form  method="post" >
				<p id="' . self::$messageId . '">' . $message .'</p>
				<input type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
	}

	//Generate login form
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

	//If username is missing
	public function usernameMissing(){
		return empty($_POST[self::$name]);
	}

	//If password is missing
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

	//If user wants to be remembered
	public function userWantsToBeRemembered(){
		return isset($_POST[self::$keep]);
	}

	//Save cookie and remember user
	public function rememberUser(){
		$hashedPassword = $this->model->getHashedPassword();

        $this->expirationDate = time() + (86400 * 30) * $this->number_of_days;

        //Save cookie for name and password
        $this->cookie->save(self::$cookieName, $this->getRequestUserName(), $this->expirationDate);
        $this->cookie->save(self::$cookiePassword, $hashedPassword, $this->expirationDate);

		return $hashedPassword;
	}

    public function isLoggedIn(){
        return $this->isCookieSet() || $this->model->isUserSaved();
    }

    public function forgetUser(){
        $this->cookie->delete(self::$cookieName);
        $this->cookie->delete(self::$cookiePassword);
    }

    public function isUserComingBack(){
        return $this->model->isUserSaved() === false && $this->isCookieSet();
    }

    public function isCookieSet(){
        return $this->cookie->load(self::$cookieName) && $this->cookie->load(self::$cookiePassword);
    }


    //Set message to show user
	public function setMessage($message){
		return $this->message = $message;
	}

}