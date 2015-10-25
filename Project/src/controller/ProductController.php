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
use model\dal\ProductBasketDAL;
use model\OrderItemModel;
use model\OrderModel;
use view\DefaultView;
use view\NavigationView;
use view\ProductView;

class ProductController
{
    private $persistentBasketDAL;

    private $defaultView;
    private $navView;
    private $productView;

    public function __construct(){
        $this->persistentBasketDAL = new ProductBasketDAL();

        $this->defaultView = new DefaultView();
        $this->navView = new NavigationView();
        $this->productView = new ProductView($this->navView);
    }

    public function purchaseProducts(){
        $basket = $this->productView->getAllOrderItems();

        //If the quantity of each items in basket is less than what exists in database, it returns true.
        if($basket === true) {
            try{
                $newCustomer = new \model\CustomerModel($this->productView->getCheckoutSSN(), $this->productView->getCheckoutFirstName(), $this->productView->getCheckoutLastName(), $this->productView->getCheckoutEmail());
            }catch(\model\InvalidSSNException $e){
                return Messages::$wrongSsn;
            }catch(\model\InvalidFirstNameException $e){
                return Messages::$wrongFirstName;
            }catch(\model\InvalidLastNameException $e){
                return Messages::$wrongLastName;
            }catch(\model\InvalidEmailException $e){
                return Messages::$wrongEmail;
            }

            if ($newCustomer == true) {
                $newCustomer->saveNewCustomerInRepository($newCustomer);
                $customer = $newCustomer->getCustomerBySsn($newCustomer->getSSN());

                //create order on that customer
                $order = new OrderModel($customer->getId());
                $order->saveNewOrderInRepository($order);
                $getOrder = $order->getLatestOrderByCustomerId($customer->getId());


                $products = $this->productView->getProductsToOrder();

                foreach ($products as $orderItem) {

                    $orderItemInModel = new OrderItemModel($getOrder->getId(), $orderItem->getId());

                    $orderItemInModel->saveNewOrderItemInRepository($orderItemInModel);
                    $getOrderItem = $this->productRepository->getProductById($orderItemInModel->getProductId());

                    //Reduce quantity on that product
                    $newQuantity = $getOrderItem->getQuantity() - 1;
                    $this->productRepository->reduceQuantity($orderItem->getId(), $newQuantity);
                }

                $this->productView->forgetBasket();
                $this->persistentBasketDAL->clearBasket();

                return $customer;
            }

            // TODO send email!
            //$customerEmail = $customer->getEmail();
            //$administratorEmail = \Settings::ADMIN_EMAIL;

            //$this->sendCustomerEmail($customer, $getOrder);
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