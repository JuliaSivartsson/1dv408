<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-21
 * Time: 18:09
 */

namespace controller;

use common\Messages;
use model\dal\ProductBasketDAL;
use model\LoginModel;
use model\ProductBasketModel;
use view\DefaultView;
use view\NavigationView;
use view\ProductView;
use view\RegisterView;


class MasterController
{
    private $registrationController;
    private $loginController;
    private $productController;

    private $defaultView;
    private $registerView;
    private $loginView;
    private $navView;
    private $productView;

    private $productBasketModel;
    private $loginModel;
    private $persistentBasketDAL;

    public function __construct(){
        $this->loginModel = new LoginModel();
        $this->productBasketModel = new ProductBasketModel();
        $this->persistentBasketDAL = new ProductBasketDAL();

        $this->defaultView = new DefaultView();
        $this->navView = new NavigationView();
        $this->registerView = new RegisterView();
        $this->productView = new ProductView($this->navView);

        $this->productController = new ProductController();
        $this->registrationController = new RegistrationController($this->registerView, $this->defaultView);

    }

    public function main()
    {
        $this->productView->setMessage("");
        $this->loginView = new \view\LoginView($this->loginModel);
        $this->loginController = new \controller\LoginController($this->loginView, $this->defaultView);

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

            //If user is coming back with cookies
            if ($this->loginView->isUserComingBack()) {
                $this->productView->setMessage(Messages::$userReturning);
            }
        }

        //If user wants to buy products
        if ($this->productView->wantToPurchase()) {
            $purchase = $this->productController->purchaseProducts();
            if ($purchase === Messages::$orderCouldNotBeCreated) {
                $this->productView->setMessage(Messages::$orderCouldNotBeCreated);
            } else if (is_string($purchase)) {
                $this->productView->setMessage($purchase);
            }else if(is_object($purchase)) {
                $this->productView->setSuccessMessage(Messages::$orderComplete . " " . $this->navView->getViewReceiptLink('Customer=' . $purchase->getId(), 'View receipt'));
                return $this->productView->viewAllProducts();
            }
        }

        if ($this->productView->wantsToAddProductToBasket()) {
            $productNameInBasket = $this->productView->rememberBasketForUser();
            $howLongWillBasketBeRemembered = $this->loginView->getExpirationDate();

            //Set cookie
            $this->productBasketModel->saveExpirationDate($howLongWillBasketBeRemembered);
            $this->productBasketModel->savePersistentBasket($productNameInBasket);
            $this->productView->setMessage(\common\Messages::$productSavedToBasket);
        }


        //If user wants to register
        if($this->registerView->registerAttempt()){
            if($this->registrationController->doRegister()){
                $this->loginView->reloadLoginPage();
                $this->loginView->setFlashSuccessMessage(Messages::$successfulRegistration);
            }
        }

        return $this->runAction();
    }

    private function runAction()
    {
        switch($this->navView->getAction()){

            case \view\NavigationView::ViewProduct :
                return $this->navView->getBackLink() . $this->productView->viewProduct();
                break;
            case \view\NavigationView::LoginUser :
                return $this->navView->getBackLink() . $this->loginView->response();
                break;
            case \view\NavigationView::ViewBasket :
                return $this->navView->getBackLink() . $this->productView->ViewBasket();
                break;
            case \view\NavigationView::RemoveItemFromBasket :
                return $this->navView->getBackLink() . $this->productController->removeItemFromBasket();
                break;
            case \view\NavigationView::RemoveOneItemFromBasket :
                return $this->navView->GetBackLink() . $this->productController->removeOneItemFromBasket();
                break;
            case \view\NavigationView::RegisterUser :
                return $this->navView->getBackLink() . $this->registerView->response();
                break;
            case \view\NavigationView::ViewCheckout :
                return $this->navView->getBackLink() . $this->productView->viewCheckout();
                break;
            case \view\NavigationView::ViewReceipt :
                return $this->navView->GetBackLink() . $this->productView->viewReceipt();
                break;
            default:
                return $this->productView->viewAllProducts();
                break;
        }
    }


}