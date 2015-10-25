<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-25
 * Time: 08:37
 */

namespace model;


use model\dal\OrderItemRepository;

class OrderItemCatalog
{
    private $orderItemRepository;

    public function __construct(){
        $this->orderItemRepository = new OrderItemRepository();
    }

    public function getAllOrderItemsWithOrderId($id){
        return $this->orderItemRepository->getAllOrderItemsWithOrderId($id);
    }

}