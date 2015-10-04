<?php

namespace model;

class ProductModel{

    private $id;
    private $description;
    private $name;
    private $price;


    public function __construct($name, $price, $description, $id = 0){
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->id = $id;

        //$this->products = new dal\ProductRepository($this->id);

    }

    /*public function getAll(){
        return $this->productRepository->getAllProducts();
    }*/

    public function getName(){
        return $this->name;
    }

    public function getPrice(){
        return $this->price;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getId(){
        return $this->id;
    }

    public function setID($id){
        $this->id = $id;
    }
}