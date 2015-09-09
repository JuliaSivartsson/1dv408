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

	private $model;
	private $message;

	public function __construct(\model\LoginModel $loginModel){
		$this->model = $loginModel;
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

    //Set message to show user
	public function setMessage($message){
		return $this->message = $message;
	}

}