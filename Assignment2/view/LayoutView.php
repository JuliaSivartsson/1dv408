<?php

namespace view;

require_once('LoginView.php');

class LayoutView
{

  public function getHTML($isLoggedIn, LoginView $loginView)
  {

    echo '<!DOCTYPE html>
      <html>
        <head>
          <meta charset="utf-8">
          <title>Login Example</title>
        </head>
        <body>
          <h1>Assignment 2</h1>
          ' . $this->renderIsLoggedIn($isLoggedIn) . '
          
          <div class="container">
              ' . $loginView->response($isLoggedIn) . '
              
              ' . $this->showDateTime() . '
          </div>
         </body>
      </html>
    ';
  }

  //If user is logged in or not
  private function renderIsLoggedIn($isLoggedIn)
  {
    if ($isLoggedIn) {
      return '<h2>Logged in</h2>';
    } else {
      return '<h2>Not logged in</h2>';
    }
  }

  //Show date and time
  public function showDateTime()
  {
    date_default_timezone_set('Europe/Stockholm');

    $timeString = '<p>' . date("l,") . ' the ' . date("jS") . ' of ' . date("F Y,") . ' The time is' . date(" H:i:s") . '</p>';

    return $timeString;
  }

}