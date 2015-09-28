<?php

namespace common;

class Messages{

    //Messages to show to user in different situations
    public static $login = "Welcome";
    public static $logout = "Bye bye!";
    public static $usernameEmpty = "Username is missing";
    public static $passwordEmpty = "Password is missing";
    public static $wrongCredentials = "Wrong name or password";
    public static $keepUserSignedIn = "Welcome and you will be remembered";
    public static $userReturning = "Welcome back with cookie";
    public static $notOkayUser = "Wrong information in cookies";


    public static $usernameTooShort = "Username has too few characters, at least 3 characters.";
    public static $passwordTooShort = "Password has too few characters, at least 6 characters.";
    public static $userExists = "User exists, pick another username.";
    public static $passwordIsNotSame = "Passwords do not match.";
    public static $successfulRegistration = "Registered new user.";

}