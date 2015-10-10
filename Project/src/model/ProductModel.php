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

    public function getId(){
        return $this->id;
    }

    public function setID($id){
        $this->id = $id;
    }

    public function getImage($imgSize = 220){
        $pathToImage = "src/images/";
        $imageExtention = ".jpg";
        $image = '<img src="'.$pathToImage.$this->getName(). $imageExtention .'" width="'. $imgSize .'" alt="image">';

        return $image;
    }
}