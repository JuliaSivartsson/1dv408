<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-21
 * Time: 18:09
 */

namespace controller;

use common\Messages;


class MasterController
{
    private $registrationController;
    private $loginController;

    private $defaultView;
    private $registerView;
    private $loginView;
    private $navView;
    private $productView;
    private $productBasketView;
    private $productController;

    private $productBasketModel;
    private $loginModel;

    private $productRepository;
    private $customerRepository;
    private $orderItemRepository;
    private $orderRepository;

    private $persistentBasketDAL;

    public function __construct(){
        $this->loginModel = new \model\LoginModel();
        $this->productRepository = new \model\dal\ProductRepository();
        $this->productBasketModel = new \model\ProductBasketModel();
        $this->orderItemRepository = new \model\dal\OrderItemRepository();
        $this->orderRepository = new \model\dal\OrderRepository();
        $this->customerRepository = new \model\dal\CustomerRepository();
        $this->productController = new \controller\ProductController();

        $this->defaultView = new \view\DefaultView();
        $this->navView = new \view\NavigationView();
        $this->productBasketView = new \view\ProductBasketView();
        $this->registerView = new \view\RegisterView();
        $this->productView = new \view\ProductView($this->productRepository, $this->navView);

        $this->persistentBasketDAL = new \model\dal\ProductBasketDAL();
        $this->registrationController = new \controller\RegistrationController($this->registerView, $this->defaultView);

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
            $this->registrationController->doRegister();
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