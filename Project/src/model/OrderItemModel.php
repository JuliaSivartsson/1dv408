<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-15
 * Time: 11:51
 */

namespace model;


class OrderItemModel
{
    private $id;
    private $orderId;
    private $productId;


    public function __construct($orderId, $productId, $id = 0){
        $this->orderId = $orderId;
        $this->productId = $productId;
        $this->id = $id;
    }

    public function getOrderId(){
        return $this->orderId;
    }

    public function getProductId(){
        return $this->productId;
    }

    public function getId(){
        return $this->id;
    }

}