<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-25
 * Time: 08:39
 */

namespace model;


use model\dal\OrderRepository;

class OrderCatalog
{

    private $orderRepository;

    public function __construct(){
        $this->orderRepository = new OrderRepository();
    }

    public function getLatestOrderByCustomerId($id){
        return $this->orderRepository->getLatestOrderByCustomerId($id);
    }

}