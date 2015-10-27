<?php

namespace model;

use model\dal\ProductRepository;

class ProductModel{

    private $id;
    private $description;
    private $name;
    private $price;
    private $quantity;
    private $productRepository;


    public function __construct($name, $price, $description, $quantity, $id = 0){
        $this->name = $name;
        $this->price = $price;
        $this->description = $description;
        $this->quantity = $quantity;
        $this->id = $id;

        $this->productRepository = new ProductRepository();
    }

    public function saveProduct(){

    }

    public function getName(){
        return $this->name;
    }

    public function getPrice(){
        return $this->price;
    }

    public function getDescription(){
        return $this->description;
    }

    public function getQuantity(){
        return $this->quantity;
    }

    public function getId(){
        return $this->id;
    }

    public function setID($id){
        $this->id = $id;
    }

    public function getProductsPagination($page, $limit){
        return $this->productRepository->getProductsPagination($page, $limit);
    }

    public function getAllProducts(){
        var_dump('hej');
        return $this->productRepository->getAllProducts();
    }

    public function getImage($id, $imgSize = 220){
        $pathToImage = "src/images/";
        $imageExtention = ".jpg";
        $image = '<img src="'.$pathToImage.$id. $imageExtention .'" width="'. $imgSize .'" alt="image">';

        return $image;
    }

    public function getProductById($id){
        return $this->productRepository->getProductById($id);
    }

}