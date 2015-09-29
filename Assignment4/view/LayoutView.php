<?php

namespace view;

class LayoutView
{
  private static $registerUser = "register";

  /**
   * @param $isLoggedIn
   * @param LoginView $loginView
   */
  public function render($isLoggedIn, IView $view)
  {

    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Register Example</title>
        </head>
        <body>
          <h1>Assignment 4</h1>
          '. $this->renderRegisterLink($isLoggedIn, $view) . $this->renderIsLoggedIn($isLoggedIn) . '

          <div class="container">
              ' . $view->response() . '

              ' . $this->showDateTime() . '
          </div>
         </body>
      </html>
    ';
  }

  /**
   * @param $isLoggedIn
   * @return string
   */
  private function renderIsLoggedIn($isLoggedIn)
  {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    } else {
      return '<h2>Not logged in</h2>';
    }
  }

  private function renderRegisterLink($isLoggedIn, $view){
    if(get_class($view) === 'view\RegisterView'){
        return "<a href='?'>Back to login</a>";
    }
    else if($isLoggedIn === false){
        return "<a href='?" . self::$registerUser . "'>Register a new user</a>";

    }
    else{
      return null;
    }
  }

  public function UserWantsToRegister(){
    return isset($_GET[self::$registerUser]);
  }

  /**
   * @return string
   */
  public function showDateTime()
  {
    date_default_timezone_set('Europe/Stockholm');

    $timeString = '<p>' . date("l,") . ' the ' . date("jS") . ' of ' . date("F Y,") . ' The time is' . date(" H:i:s") . '</p>';

    return $timeString;
  }

}