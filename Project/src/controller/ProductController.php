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
    private $registerView;
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
    private $persistentBasketDAL;
    private $registrationController;
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
            case \view\NavigationView::RemoveItemFromBasket :
                return $this->navView->GetBackLink() . $this->removeItemFromBasket();
                break;
            case \view\NavigationView::RegisterUser :
                return $this->navView->GetBackLink() . $this->registerView->response();
                break;
            case \view\NavigationView::ViewCheckout :
                return $this->navView->GetBackLink() . $this->productView->viewCheckout();
                break;
            /*case \view\NavigationView::EditBoat :
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
        $this->persistentBasketDAL = new \model\dal\ProductBasketDAL();

        $this->defaultView = new \view\DefaultView();
        $this->navView = new \view\NavigationView();
        $this->productBasketView = new \view\ProductBasketView();
        $this->registerView = new \view\RegisterView();

        $this->loginModel = new \model\LoginModel();
        $this->loginView = new \view\LoginView($this->loginModel);
        $this->loginController = new \controller\LoginController($this->loginView, $this->defaultView);
        $this->registrationController = new \controller\RegistrationController($this->registerView, $this->defaultView);

        $this->productView = new \view\ProductView($this->productRepository, $this->navView);

        $this->loginView->getFlashMessage();

        if ($this->loginController->isUserOkay()) {
            //If user wants to login
            if ($this->loginView->loginAttempt() && $this->loginView->isLoggedIn() == FALSE) {
                $this->loginController->doLogin();
                if ($this->loginController->doLogin()) {
                    $this->loginView->reloadPage();
                }
            }
            //If user wants to logout
            if ($this->loginView->logoutAttempt() && $this->loginView->isLoggedIn() == TRUE) {
                $this->loginController->doLogout();
            }

            if ($this->loginView->isUserComingBack()) {
                $this->loginView->setMessage(Messages::$userReturning);
            }

            //If user wants to remove item from basket
            if ($this->productView->removeItemFromBasket()) {
                $this->removeItemFromBasket();
            }
        }

        if($this->productView->wantsToAddProductToBasket()){
            $productNameInBasket = $this->productView->rememberBasketForUser();
            $howLongWillBasketBeRemembered = $this->loginView->getExpirationDate();

            //Set cookie
            $this->productBasketModel->saveExpirationDate($howLongWillBasketBeRemembered);
            $this->productBasketModel->savePersistentBasket($productNameInBasket);
            $this->productView->setMessage(\common\Messages::$productSavedToBasket);
        }

        if($this->productView->orderBasket()){
            $this->doOrder();
        }

        //If user wants to register
        if($this->registerView->registerAttempt()){
            $this->registrationController->doRegister();
        }

        return $this->RunAction();
    }

    public function doOrder(){
        //get name products to order from view
        $products = $this->productView->getProductsToOrder();



    }

    public function removeItemFromBasket()
    {
        $getItemToRemove = $this->productView->getItemToRemoveFromBasket();
        $this->persistentBasketDAL->removeLineFromFile($getItemToRemove->getName());

        return $this->productView->viewBasket();
    }


}