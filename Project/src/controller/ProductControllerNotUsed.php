<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-04
 * Time: 09:52
 */

namespace controller;


use common\Messages;
use model\CustomerModel;
use model\OrderItemModel;
use model\OrderModel;

class ProductControllerNotUsed
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
    private $customerModel;

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
        //$this->customerModel = new \model\CustomerModel();
        //$this->customerRepository = new \model\dal\CustomerRepository();

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

            //If user is coming back with cookies
            if ($this->loginView->isUserComingBack()) {
                $this->productView->setMessage(Messages::$userReturning);
            }

            //If user wants to buy products
            if($this->productView->wantToPurchase()){
                $this->purchaseProducts();
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
            case \view\NavigationView::RemoveOneItemFromBasket :
                return $this->navView->GetBackLink() . $this->removeOneItemFromBasket();
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
            default:
                return $this->productView->viewAllProducts();
                break;
        }
    }

    public function purchaseProducts(){

        $basket = $this->productView->getAllOrderItems();

        //If the quantity of each items in basket is less than what exists in database, it returns true.
        if($basket === true) {

            $newCustomer = $this->validateOrder($this->productView->getCheckoutSSN(), $this->productView->getCheckoutFirstName(), $this->productView->getCheckoutLastName(), $this->productView->getCheckoutEmail());

            //If we create a new customer
            if ($newCustomer == true) {
                $newCustomer->saveNewCustomerInRepository($newCustomer);
                $newCustomer->getCustomerBySsn($newCustomer->getSSN());
                $customer = $newCustomer->getCustomerBySsn($newCustomer->getSsn());

                //create order on that customer
                $order = new OrderModel($customer->getId());
                $this->orderRepository->save($order);
                $getOrder = $this->orderRepository->getLatestOrderByCustomerId($customer->getId());

                $products = $this->productView->getProductsToOrder();

                foreach ($products as $orderItem) {
                    $orderItemInModel = new OrderItemModel($getOrder->getId(), $orderItem->getId());
                    $this->orderItemRepository->save($orderItemInModel);
                    $getOrderItem = $this->productRepository->getProductById($orderItemInModel->getProductId());

                    //Reduce quantity on that product
                    $newQuantity = $getOrderItem->getQuantity() -1;
                    $this->productRepository->reduceQuantity($orderItem->getId(), $newQuantity);
                }

                $this->productView->setSuccessMessage(Messages::$orderComplete . " " . $this->navView->getViewReceiptLink('Customer=' . $customer->getId(), 'View receipt'));
                $this->productView->forgetBasket();
                $this->persistentBasketDAL->clearBasket();

                $customerEmail = $customer->getEmail();
                $administratorEmail = \Settings::ADMIN_EMAIL;

                // TODO send email!
                //$this->sendCustomerEmail($customer, $getOrder);


            } else {
                return $this->productView->viewCheckout();
            }
        }
        else{
            $this->productView->setMessage(Messages::$orderCouldNotBeCreated);
        }
    }

    public function validateOrder($ssn, $firstName, $lastName, $email){
        try{
            $customer = new \model\CustomerModel($ssn, $firstName, $lastName, $email);
            return $customer;
        }catch(\model\InvalidSSNException $e){
            $this->productView->setMessage("Social security number must be in correct format (xxxxxxxx-xxxx)");
        }catch(\model\InvalidFirstNameException $e){
            $this->productView->setMessage("Firstname must be atleast 3 characters long and only contain valid characters.");
        }catch(\model\InvalidLastNameException $e){
            $this->productView->setMessage("Lastname must be atleast 3 characters long and only contain valid characters.");
        }catch(\model\InvalidEmailException $e){
            $this->productView->setMessage("Email must be a valid email address");
        }
        return false;
    }

    public function removeItemFromBasket()
    {
        $getItemToRemove = $this->productView->getItemToRemoveFromBasket();
        $this->persistentBasketDAL->removeLinesFromFile($getItemToRemove->getName());

        $this->productView->setMessage("Removed");
        return $this->productView->viewBasket();
    }

    public function removeOneItemFromBasket()
    {
        $getItemToRemove = $this->productView->getItemToRemoveFromBasket();
        $this->persistentBasketDAL->removeOneLineFromFile($getItemToRemove->getName());
        $this->productView->setMessage(Messages::$removedOneItem);
        return $this->productView->viewBasket();
    }

    private function sendCustomerEmail(CustomerModel $customer, OrderModel $order){

        $orderId = $order->getId();
        var_dump($order->getId());
        var_dump($customer->getEmail());

        $to      = $customer->getEmail();
        $subject = 'Your order!';
        $message = 'hello';
        $headers = 'From: itzys webshop';

        if(mail($to, $subject, $message, $headers)){
            return true;
        }
        else{
            return false;
        }

    }
}