<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-04
 * Time: 09:52
 */

namespace controller;


class ProductController
{

    private $defaultView;
    private $navigationView;
    private $loginView;

    /*public function __construct(){
        $this->defaultView = new \view\DefaultView();
        $this->navigationView = new \view\NavigationView();

        $this->products = new \model\ProductModel();
    }*/

    /* @var $memberView \view\Member */
    private $productView;
    private $productController;
    private $productBasketView;

    /* @var $navView \view\NavigationView */
    private $navView;

    /* @var $navView \model\dal\MemberRepository */
    private $userRepository;
    private $loginModel;

    /* @var $navView \model\dal\BoatRepository */
    private $productRepository;
    private $productBasketRepository;
    private $login;
    private $loginController;

    public function RunAction()
    {

        switch($this->navView->GetAction()){

            case \view\NavigationView::ViewProduct :
                return $this->navView->getBackLink() . $this->productView->viewProduct();
                break;
            case \view\NavigationView::LoginUser :
                return $this->navView->GetBackLink() . $this->loginView->response();
                break;
            /*case \view\NavigationView::ViewAllProducts :
                return $this->navView->GetBackLink() . $this->productView->ViewAllProducts();
                break;
            case \view\NavigationView::EditMember :
                return $this->navView->GetBackLink() . $this->EditMember();
                break;
            case \view\NavigationView::AddMember :
                return $this->navView->GetBackLink() . $this->AddMember();
                break;
            case \view\NavigationView::DeleteMember :
                return $this->navView->GetBackLink() . $this->DeleteMember();
                break;
            case \view\NavigationView::EditBoat :
                return $this->navView->GetBackLink() . $this->EditBoat();
                break;
            case \view\NavigationView::AddBoat :
                return $this->navView->GetBackLink() . $this->AddBoat();
                break;
            case \view\NavigationView::DeleteBoat :
                return $this->navView->GetBackLink() . $this->DeleteBoat();
                break;*/
            default:
                return $this->productView->viewAllProducts();
                break;
        }
    }

    public function Main(){
        $this->userRepository = new \model\dal\UserRepository();
        $this->productRepository = new \model\dal\ProductRepository();
        $this->productBasketRepository = new \model\dal\ProductBasketRepository();

        $this->defaultView = new \view\LayoutView();
        $this->navView = new \view\NavigationView();
        $this->productBasketView = new \view\ProductBasketView();
        $this->loginModel = new \model\LoginModel();
        $this->loginView = new \view\LoginView($this->loginModel);
        $this->loginModel = new \model\LoginModel();
        $this->loginController = new \controller\LoginController($this->loginView, $this->defaultView);

        $this->productView = new \view\ProductView($this->productRepository, $this->navView);

        $this->productView->getFlashMessage();


        if($this->productView->wantsToAddProductToBasket()){
            $productToAdd = $this->productView->getProductToAdd();
            $this->productBasketRepository->addItem($productToAdd);
            $this->productView->setMessage(\common\Messages::$productSavedToBasket);
        }

        return $this->RunAction();


        //User pushes login-link
        /*if($this->loginView->loginAttempt() && $this->loginView->isLoggedIn() == FALSE) {
            if($this->loginView->usernameMissing()) {
                var_dump('fattas');
                $this->productView->setMessage(Messages::$usernameEmpty);
                return $this->navView->reloadLogin();
            }
            else{$this->doLogin();}
            //$this->login = new \controller\LoginController($this->loginView, $this->defaultView);
            //$this->productView->viewAllProducts();
        }
        else{
            return $this->RunAction();
        }
*/
        //$this->productView->getFlashMessage();

        /*if ($this->isUserOkay()) {
            //If user tries to login and is not logged in
            if ($this->loginView->loginAttempt() && $this->loginView->isLoggedIn() == FALSE) {
                $this->loginController->doLogin();
            }

            //If user tries to logout and is logged in
            if ($this->loginView->logoutAttempt() && $this->loginView->isLoggedIn() == TRUE) {
                $this->loginController->doLogout();
                $this->productView->setFlashMessage(Messages::$logout);
                $this->productView->reloadPage();
            }

            //If user is coming back with cookie
            if ($this->loginView->isUserComingBack()) {
                $this->loginView->setMessage(Messages::$userReturning);
            }

        }

        if($this->navView->GetAction() == \view\NavigationView::ViewProduct ) {
            $this->productView->viewProduct();
        }

            //$this->defaultView->getHTML($this->loginView->isLoggedIn(), $this->productView);
            //If user pressed button to add product to basket
            if ($this->productView->wantsToAddProductToBasket()) {

                $productToAdd = $this->productView->getProductToAdd();
                $this->productBasketRepository->addItem($productToAdd);

                $this->productView->setFlashMessage(Messages::$addToBasket);
                $this->productView->reloadPage();
            } else {

                $this->defaultView->getHTML($this->loginView->isLoggedIn(), $this->productView);
            }*/
    }


    public function doLogin(){
        //Get info from view

        $username = $this->loginView->getRequestUserName();
        $password = $this->loginView->getRequestPassword();
        $user = $this->userRepository->getUserByUsername($username);

        if($this->loginView->usernameMissing()){

            $this->productView->setMessage(Messages::$usernameEmpty);
            return;
            //return $this->loginView->response();
        }
        else if($this->loginView->passwordMissing()){
            $this->loginView->setMessage(Messages::$passwordEmpty);
            //return $this->defaultView->getHTML($this->loginView->isLoggedIn(), $this->masterController->Main());
        }
        //If credentials are correct
        if($this->userRepository->getUserByUsername($username) !== null && $user->authenticateLogin($username, $password)) {
            $this->loginView->setMessage(Messages::$login);
            $this->loginModel->login($username, $password);
            if ($this->loginView->userWantsToBeRemembered()) {
                //Get hashed password and expirationdate
                $passwordToIdentifyUser = $this->loginView->rememberUser();
                $howLongWillUserBeRemembered = $this->loginView->getExpirationDate();
                //Set cookie
                $this->loginModel->saveExpirationDate($howLongWillUserBeRemembered);
                $this->loginModel->savePersistentLogin($passwordToIdentifyUser);
                $this->loginView->setMessage(Messages::$keepUserSignedIn);
            }
            $this->hasLoggedIn = true;
            return true;
        }
        else{
            $this->loginView->setMessage(Messages::$wrongCredentials);
        }
    }


    public function isUserOkay(){
        if($this->loginView->didUserChangeCookie()){
            $this->doLogout();
            $this->loginView->setMessage(Messages::$notOkayUser);
            return false;
        } else if($this->loginModel->isUserTheRightUser($this->loginView->getUserIdentifier()) === false){
            $this->doLogout();
            return false;
        }
        else
        {
            return true;
        }
    }

    public function AddProductToBasket(){

    }


}