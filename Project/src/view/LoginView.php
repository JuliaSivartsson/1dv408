<?php

namespace view;

use \common\Messages;

class LoginView{

	private static $login = 'LoginView::Login';
	private static $logout = 'LoginView::Logout';
	private static $name = 'LoginView::UserName';
	private static $password = 'LoginView::Password';
	private static $cookieName = 'LoginView::CookieName';
	private static $cookiePassword = 'LoginView::CookiePassword';
	private static $keep = 'LoginView::KeepMeLoggedIn';
	private static $messageId = 'LoginView::Message';
	private static $flashLocation = 'LoginView::FlashMessage';
	private static $flashSuccessLocation = 'LoginView::FlashSuccessMessage';

    private $expirationDate;
	private $model;
	private $message;
	private $successMessage;

	const SAVED_USERNAME = 'savedname';

	public function __construct(\model\LoginModel $loginModel){
		$this->model = $loginModel;
        $this->cookie = new \common\CookieStorage();
	}

	public function response() {
		$message = $this->message;

		if($this->isLoggedIn()){
			$response = $this->generateLogoutButtonHTML($message);
		}
		else{
			$response = $this->generateLoginFormHTML($message);
		}
		return $response;

	}

	public function getFlashMessage()
	{
		if ($this->cookie->isCookieSet(self::$flashLocation)) {
			$this->message = $this->cookie->load(self::$flashLocation);
			$this->cookie->delete(self::$flashLocation);
		}
		else if ($this->cookie->isCookieSet(self::$flashSuccessLocation)) {
			$this->successMessage = $this->cookie->load(self::$flashSuccessLocation);
			$this->cookie->delete(self::$flashSuccessLocation);
		}
	}
	public function setFlashMessage($message) {
		assert(is_string($message), "LoginView::setMessage needs a string as argument");
		$this->cookie->save(self::$flashLocation, $message, time() + 3600);
	}

	public function setFlashSuccessMessage($message) {
		assert(is_string($message), "LoginView::setMessage needs a string as argument");
		$this->cookie->save(self::$flashSuccessLocation, $message, time() + 3600);
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

		$successMessage = $this->successMessage;


		if($successMessage != ""){
			$successMessageContainer = '<div class="checkoutMessage"><p class="alert alert-success" id="' . self::$messageId . '">' . $successMessage . '</p></div>';
		}
		else{
			$successMessageContainer = "";
		}

		if($message != ""){
			$messageContainer = '<div class="checkoutMessage "><p class="alert alert-danger" id="' . self::$messageId . '">' . $message . '</p></div>';
		}
		else{
			$messageContainer = '<p" id="' . self::$messageId . '">' . $message . '</p>';
		}

        return '
			<div class="jumbotron">
				<form method="post" >
					<fieldset>
						<legend>Login - enter Username and password</legend>

						<div class="normal-font">
                        	'. $successMessageContainer .'
							'. $messageContainer .'
						</div>
						<label for="' . self::$name . '">Username :</label>
						<input type="text" id="' . self::$name . '" name="' . self::$name . '" value="' . $this->getUsernameToDisplay()  .'" />
						<label for="' . self::$password . '">Password :</label>
						<input type="password" id="' . self::$password . '" name="' . self::$password . '" />
						<label for="' . self::$keep . '">Keep me logged in  :</label>
						<input type="checkbox" id="' . self::$keep . '" name="' . self::$keep . '" />

						<input type="submit" name="' . self::$login . '" value="login" />
					</fieldset>
				</form>
			</div>
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

	public function getUsernameToDisplay(){
		if(isset($_POST[self::$name])){
			$this->cookie->delete(self::SAVED_USERNAME);
			return $_POST[self::$name];
		}
		else if($this->cookie->isCookieSet(self::SAVED_USERNAME)){
			return $this->cookie->load(self::SAVED_USERNAME);
		}
		else{
			return "";
		}
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
            return $this->model->getStoredPassword() !== $this->cookie->load(self::$cookiePassword) ||
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

	/**
	 * Gets information about the user
	 * Is used to check if someone is using session hijacking
	 * @return mixed
	 */
	public function getUserIdentifier(){
		return $_SERVER['HTTP_USER_AGENT'];
	}
	
	public function reloadPage(){
		header('Location: /project-inlog/index.php' );
	}

	//TODO Is this correct??
	public function reloadLoginPage(){
		header('Location: /project-inlog/index.php?action=LoginUser' );
	}

    //Set message to show user
	public function setMessage($message){
        assert(is_string($message));
		return $this->message = $message;
	}
}