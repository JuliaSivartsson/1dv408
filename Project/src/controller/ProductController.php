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

class ProductController
{
    private $defaultView;
    private $navView;
    private $productView;
    private $productBasketView;
    private $productRepository;
    private $customerRepository;
    private $orderItemRepository;
    private $orderRepository;

    private $persistentBasketDAL;

    public function __construct(){
        $this->productRepository = new \model\dal\ProductRepository();
        $this->orderItemRepository = new \model\dal\OrderItemRepository();
        $this->orderRepository = new \model\dal\OrderRepository();
        $this->customerRepository = new \model\dal\CustomerRepository();

        $this->defaultView = new \view\DefaultView();
        $this->navView = new \view\NavigationView();
        $this->productBasketView = new \view\ProductBasketView();
        $this->productView = new \view\ProductView($this->productRepository, $this->navView);

        $this->persistentBasketDAL = new \model\dal\ProductBasketDAL();
    }

    public function purchaseProducts(){
        $basket = $this->productView->getAllOrderItems();

        //If the quantity of each items in basket is less than what exists in database, it returns true.
        if($basket === true) {
            try{
                $newCustomer = new \model\CustomerModel($this->productView->getCheckoutSSN(), $this->productView->getCheckoutFirstName(), $this->productView->getCheckoutLastName(), $this->productView->getCheckoutEmail());
            }catch(\model\InvalidSSNException $e){
                return "Social security number must be in correct format (xxxxxxxx-xxxx)";
            }catch(\model\InvalidFirstNameException $e){
                return "Firstname must be atleast 3 characters long and only contain valid characters.";
            }catch(\model\InvalidLastNameException $e){
                return "Lastname must be atleast 3 characters long and only contain valid characters.";
            }catch(\model\InvalidEmailException $e){
                return "Email must be a valid email address";
            }

            if ($newCustomer == true) {
                $this->customerRepository->save($newCustomer);
                $customer = $this->customerRepository->getCustomerBySsn($newCustomer->getSSN());

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
                    $newQuantity = $getOrderItem->getQuantity() - 1;
                    $this->productRepository->reduceQuantity($orderItem->getId(), $newQuantity);
                }

                $this->productView->forgetBasket();
                $this->persistentBasketDAL->clearBasket();

                //$this->productView->setSuccessMessage(Messages::$orderComplete . " " . $this->navView->getViewReceiptLink('Customer=' . $customer->getId(), 'View receipt'));
                return $customer;
            }

            //$newCustomer = $this->validateOrder($this->productView->getCheckoutSSN(), $this->productView->getCheckoutFirstName(), $this->productView->getCheckoutLastName(), $this->productView->getCheckoutEmail());

            //If we create a new customer

                //$customerEmail = $customer->getEmail();
                //$administratorEmail = \Settings::ADMIN_EMAIL;

                // TODO send email!
                //$this->sendCustomerEmail($customer, $getOrder);

/*
            } else {
                return $this->productView->setMessage('fel');
                //return $this->productView->viewCheckout();
            }*/
        }
        else{
            $this->productView->setMessage(Messages::$orderCouldNotBeCreated);
            return Messages::$orderCouldNotBeCreated;
        }
    }

    public function removeItemFromBasket()
    {
        $getItemToRemove = $this->productView->getItemToRemoveFromBasket();
        $this->persistentBasketDAL->removeLinesFromFile($getItemToRemove->getName());

        $this->productView->setMessage(Messages::$removedItems);
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