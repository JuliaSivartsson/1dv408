<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-14
 * Time: 10:20
 */

namespace model;

use model\dal\OrderRepository;

class OrderModel
{
    private $id;
    private $customerId;
    private $orderRepository;


    public function __construct($customerId, $id = 0){
        $this->customerId = $customerId;
        $this->id = $id;

        $this->orderRepository = new OrderRepository();
    }

    public function getCustomerId(){
        return $this->customerId;
    }

    public function getId(){
        return $this->id;
    }

    public function saveNewOrderInRepository(OrderModel $newOrder){
        $this->orderRepository->save($newOrder);
    }

    public function getLatestOrderByCustomerId($customerId){
        return $this->orderRepository->getLatestOrderByCustomerId($customerId);
    }


}