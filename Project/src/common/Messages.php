<?php

namespace common;

class Messages{

    //Login specific messages
    public static $login = "Welcome";
    public static $logout = "Bye bye!";
    public static $usernameIsNotCorrect = "Username is empty or has too few characters (needs at least 3 characters.)";
    public static $passwordIsNotCorrect = "Password is empty or has too few characters (needs at least 6 characters.)";
    public static $wrongCredentials = "Wrong name or password";
    public static $keepUserSignedIn = "Welcome and you will be remembered";
    public static $userReturning = "Welcome back with cookie";
    public static $notOkayUser = "Wrong information in cookies";

    //User registration validation
    public static $userExists = "User exists, pick another username.";
    public static $passwordIsNotSame = "Passwords do not match.";
    public static $forbiddenCharacters = "Username contains invalid characters.";
    public static $successfulRegistration = "Registered new user.";

    //Basket and order specific messages
    public static $productSavedToBasket = "This product has been added to your basket.";
    public static $orderComplete = "Thank you for your order!";
    public static $orderCouldNotBeCreated = "Something went wrong! Make sure the quantity of your ordered objects exists.";
    public static $removedOneItem = "Removed one item from basket.";
    public static $removedItems = "Removed all items of this kind in basket.";

    //Create customer from validation
    public static $wrongSsn = "Social security number must be in correct format (xxxxxxxx-xxxx)";
    public static $wrongFirstName = "Firstname must be atleast 3 characters long and only contain valid characters.";
    public static $wrongLastName = "Lastname must be atleast 3 characters long and only contain valid characters.";
    public static $wrongEmail = "Email must be a valid email address";


}