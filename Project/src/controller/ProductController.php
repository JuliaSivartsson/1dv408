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

    private $registrationController;
    private $loginController;

    private $defaultView;
    private $registerView;
    private $loginView;
    private $navView;
    private $productView;
    private $productBasketView;

    private $productBasketModel;
    private $loginModel;

    private $userRepository;
    private $productRepository;
    private $customerRepository;
    private $orderItemRepository;
    private $orderRepository;

    private $persistentBasketDAL;

    public function __construct(){

        $this->customerRepository = new \model\dal\CustomerRepository();
        $this->orderItemRepository = new \model\dal\OrderItemRepository();
        $this->orderRepository = new \model\dal\OrderRepository();
        $this->userRepository = new \model\dal\UserRepository();
        $this->productRepository = new \model\dal\ProductRepository();
        $this->productBasketModel = new \model\ProductBasketModel();

        $this->loginModel = new \model\LoginModel();

        $this->defaultView = new \view\DefaultView();
        $this->navView = new \view\NavigationView();
        $this->productBasketView = new \view\ProductBasketView();
        $this->registerView = new \view\RegisterView();
        $this->productView = new \view\ProductView($this->productRepository, $this->navView);


        $this->persistentBasketDAL = new \model\dal\ProductBasketDAL();

        $this->registrationController = new \controller\RegistrationController($this->registerView, $this->defaultView);

    }

    public function main(){
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

            if ($this->loginView->isUserComingBack()) {
                $this->loginView->setMessage(Messages::$userReturning);
            }

            if($this->productView->wantToPurchase()){
                $this->purchaseProducts();
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
                return $this->navView->getBackLink() . $this->removeItemFromBasket();
                break;
            case \view\NavigationView::RegisterUser :
                return $this->navView->getBackLink() . $this->registerView->response();
                break;
            case \view\NavigationView::ViewCheckout :
                return $this->navView->getBackLink() . $this->productView->viewCheckout();
                break;
            case \view\NavigationView::PurchaseProducts :
                return $this->navView->getBackLink() . $this->purchaseProducts();
                break;
            case \view\NavigationView::ViewReceipt :
                return $this->navView->GetBackLink() . $this->productView->viewReceipt();
                break;
            /*case \view\NavigationView::DeleteBoat :
                return $this->navView->GetBackLink() . $this->DeleteBoat();
                break;*/
            default:
                return $this->productView->viewAllProducts();
                break;
        }
    }

    private function purchaseProducts(){
        //get name products to order from view
        //$customerInformation = $this->productView->getCustomerInformation();

        //TODO if quantity in basket is larger than total quantity order should not go through!
        $basket = $this->productView->getAllOrderItems();

        //If the quantity of each items in basketis less than what exists in database, it returns true.
        if($basket === true) {

            //If we create a new customer
            if ($customerToSave = $this->productView->getCustomerInformation()) {

                //save customer in database
                $this->customerRepository->save($customerToSave);

                //get customer from database
                $customer = $this->customerRepository->getCustomerBySsn($customerToSave->getSsn());

                //create order on that customer
                $order = new \model\OrderModel($customer->getId());

                //save order in database
                $this->orderRepository->save($order);

                //get order from database
                $getOrder = $this->orderRepository->getLatestOrderByCustomerId($customer->getId());

                $products = $this->productView->getProductsToOrder();

                foreach ($products as $orderItem) {

                    //save orderitem in model
                    $orderItemInModel = new \model\OrderItemModel($getOrder->getId(), $orderItem->getId());

                    //save orderitem in database
                    $this->orderItemRepository->save($orderItemInModel);
                }
                //TODO should reduce quantity here

                $this->productView->setSuccessMessage(Messages::$orderComplete . " " . $this->navView->getViewReceiptLink('Customer=' . $customer->getId(), 'View receipt'));
                $this->productView->forgetBasket();
                $this->persistentBasketDAL->clearBasket();
                // TODO send email!

            } else {
                return $this->productView->viewCheckout();
            }
        }
        else{
            $this->productView->setMessage(Messages::$orderCouldNotBeCreated);
        }
    }

    private function removeItemFromBasket()
    {
        $getItemToRemove = $this->productView->getItemToRemoveFromBasket();
        $this->persistentBasketDAL->removeLineFromFile($getItemToRemove->getName());

        return $this->productView->viewBasket();
    }


}