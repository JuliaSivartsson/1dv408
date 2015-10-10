<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-04
 * Time: 09:52
 */

namespace controller;


use common\Messages;

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
    private $productBasketModel;

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
            case \view\NavigationView::ViewBasket :
                return $this->navView->GetBackLink() . $this->productView->ViewBasket();
                break;
            /*case \view\NavigationView::LoginMember :
                return $this->navView->GetBackLink() . $this->LoginController->render();
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
        $this->productBasketModel = new \model\ProductBasketModel();

        $this->defaultView = new \view\DefaultView();
        $this->navView = new \view\NavigationView();
        $this->productBasketView = new \view\ProductBasketView();

        $this->loginModel = new \model\LoginModel();
        $this->loginView = new \view\LoginView($this->loginModel);
        $this->loginController = new \controller\LoginController($this->loginView, $this->defaultView);

        $this->productView = new \view\ProductView($this->productRepository, $this->navView);

        $this->loginView->getFlashMessage();

        if ($this->loginView->loginAttempt() && $this->loginView->isLoggedIn() == FALSE) {
            $this->loginController->doLogin();
            if($this->loginController->doLogin()){
                $this->loginView->reloadPage();
            }
        }
        if ($this->loginView->logoutAttempt() && $this->loginView->isLoggedIn() == TRUE) {
            $this->loginController->doLogout();
        }

        if($this->productView->wantsToAddProductToBasket()){
            /*$productToAdd = $this->productView->getProductToAdd();
            $this->productBasketRepository->addItem($productToAdd);
            $this->productView->setMessage(\common\Messages::$productSavedToBasket);*/

            $productNameInBasket = $this->productView->rememberBasketForUser();
            $howLongWillBasketBeRemembered = $this->loginView->getExpirationDate();

            //Set cookie
            $this->productBasketModel->saveExpirationDate($howLongWillBasketBeRemembered);
            $this->productBasketModel->savePersistentBasket($productNameInBasket);
            $this->productView->setMessage(\common\Messages::$productSavedToBasket);
        }

        return $this->RunAction();
    }

    /*public function doLogin(){
//Get info from view
        $username = $this->loginView->getRequestUserName();
        $password = $this->loginView->getRequestPassword();
        $user = $this->userRepository->getUserByUsername($username);

        if($this->loginView->usernameMissing()){
            $this->loginView->setMessage(Messages::$usernameEmpty);
            return;
        }
        else if($this->loginView->passwordMissing()){
            $this->loginView->setMessage(Messages::$passwordEmpty);
            return;
        }
        //If credentials are correct
        if($this->userRepository->getUserByUsername($username) !== null && $user->authenticateLogin($username, $password)) {
            $this->loginView->setMessage(Messages::$login);
            $this->loginModel->login($username, $password);

            //$this->defaultView->getHTML($this->loginView->isLoggedIn(), $this->Main());

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
    }*/

    public function AddProductToBasket(){

    }


}