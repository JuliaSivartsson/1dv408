<?php


namespace view;

class DefaultView
{
    private static $loginUser = "loginUser";
    private static $logout = 'LoginView::Logout';
    private static $registerUser = "register";
    private $navView;
    private $registrationLink = "";
    private $logoutButton = "";
    private $renderBasket = "";
    private $separationMark = " | ";

    public function getHTML($isLoggedIn, $body){

        if($isLoggedIn === true){
            $this->logoutButton = $this->generateLogoutButtonHTML();
            $this->renderBasket = $this->renderBasketLink();
            $this->separationMark = "";
        }

        echo '
            <!DOCTYPE html>
            <html lang="sv">
            <head>
                <meta charset="UTF-8">
                <title>Project</title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
                <link rel="stylesheet" href="src/styles/style.css">
            </head>
            <body>

            <div class="container">
                <div class="top-banner">
                    <div class="pull-left loginDiv">
                        '. $this->renderLoginLink($isLoggedIn) . $this->separationMark . $this->renderRegisterLink($isLoggedIn) . $this->logoutButton .'
                    </div>
                    <div class="pull-right">
                        '. $this->renderBasket .'
                    </div>
                </div>
                    ' . $body . '
                </div>
            </body>
            </html>
                    ';
    }

    private function generateLogoutButtonHTML() {

        return '
			<form  method="post" >
				<input class="btn btn-primary" type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
    }

    private function renderIsLoggedIn($isLoggedIn)
    {
        if ($isLoggedIn) {
            return '<h3>Logged in</h3>';
        } else {
            return '<h3>Not logged in</h3>';
        }
    }

    private function renderRegisterLink($isLoggedIn){
        $this->navView = new \view\NavigationView();
        if($isLoggedIn === false){
            return $this->navView->GetRegisterUserLink("Register a new user");
        }
        else{
            return null;
        }
    }

    private function renderLoginLink($isLoggedIn){
        $this->navView = new \view\NavigationView();
        if($isLoggedIn === false){
            return $this->navView->GetLoginUserLink("Login");
        }
        else{
            return null;
        }
    }

    private function renderBasketLink(){
        $this->navView = new \view\NavigationView();

            return $this->navView->GetBasketLink("<i class='fa fa-shopping-cart fa-5x'></i>");

    }
}
