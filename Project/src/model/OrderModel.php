<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-14
 * Time: 10:20
 */

namespace model;

class OrderModel
{
    private $id;
    private $customerId;


    public function __construct($customerId, $id = 0){
        $this->customerId = $customerId;
        $this->id = $id;
    }

    public function getCustomerId(){
        return $this->customerId;
    }

    public function getId(){
        return $this->id;
    }


}