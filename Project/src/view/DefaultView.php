<?php


namespace view;

class DefaultView
{
    private static $loginUser = "loginUser";
    private static $logout = 'LoginView::Logout';
    private $navView;
    private $logoutButton = "";
    private $username;

    public function getHTML($isLoggedIn, $body){

        if($isLoggedIn === true){
            $this->logoutButton = $this->generateLogoutButtonHTML();
        }

        echo '
            <!DOCTYPE html>
            <html lang="sv">
            <head>
                <meta charset="UTF-8">
                <title>Project</title>
                <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">
            </head>
            <body>
                '. $this->renderLoginLink($isLoggedIn) . $this->renderIsLoggedIn($isLoggedIn) . '
                '. $this->logoutButton .'
            <div class="container">
                    ' . $body . '
                </div>
            </body>
            </html>
                    ';
    }

   /* public function Render($productModel){

        $ret = "";
        foreach($productModel as $product){
            $ret .= $product;
        }
        return '
        <h1>Welcome to itzys webshop</h1>
        <div class="jumbotron">
        foreach(){
        '. $ret .'
        </div>

        ';
    }*/

    private function generateLogoutButtonHTML() {

        return '
			<form  method="post" >
				<input class="btn btn-primary" type="submit" name="' . self::$logout . '" value="logout"/>
			</form>
		';
    }

    public function UserWantsToLogin(){
        return isset($_GET[self::$loginUser]);
    }

    /**
     * @param $isLoggedIn
     * @return string
     */
    private function renderIsLoggedIn($isLoggedIn)
    {
        if ($isLoggedIn) {
            return '<h3>Logged in</h3>';
        } else {
            return '<h3>Not logged in</h3>';
        }
    }

    private function renderLoginLink($isLoggedIn){

        $this->navView = new \view\NavigationView();
        /*if(get_class($view) === 'view\RegisterView'){
            return "<a href='?'>Back to login</a>";
        }*/
        if($isLoggedIn === false){
            return $this->navView->GetLoginUserLink("Login");

        }
        else{
            return null;
        }
    }

    private function renderLogoutLink($isLoggedIn, $view){
        if(get_class($view) === 'view\RegisterView'){
            return "<a href='?'>Back to login</a>";
        }
        else if($isLoggedIn === false){
            return "<a href='?" . self::$loginUser . "'>Login</a>";

        }
        else{
            return null;
        }
    }
}