<?php

namespace model\dal;

class ProductRepository{

    private $database;
    private static $NameColumn = 'name';
    private static $PriceColumn = 'price';

    public function __construct(){
        $this->dbTable = 'product';
        $connection = new DatabaseConnection();

        try{
            $this->database = $connection->SetupDatabase();
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function getAllProducts()
    {
        $ret = array();

        $stmt = $this->database->prepare("SELECT * FROM  $this->dbTable ORDER BY ". self::$NameColumn ."");
        $stmt->execute();

        while($product = $stmt->fetchObject()){
            $ret[] =  new \model\ProductModel($product->name, $product->price, $product->description, $product->id);
        }

        return $ret;
    }

    public function getProductById($id){
        $stmt = $this->database->prepare("SELECT * FROM  $this->dbTable WHERE id = ?");
        $stmt->execute(array($id));

        if($product = $stmt->fetchObject()){
            return new \model\ProductModel($product->name, $product->price, $product->description);
        }

        throw new \Exception("Product not found");
    }

    public function getProductByName($name){
        $stmt = $this->database->prepare("SELECT * FROM $this->dbTable WHERE ". self::$NameColumn ." = ?");
        $stmt->execute(array($name));

        if($product = $stmt->fetchObject()){
            return new \model\ProductModel($product->name, $product->price, $product->description, $product->id);
        }

        throw new \Exception("Product not found");
    }
}