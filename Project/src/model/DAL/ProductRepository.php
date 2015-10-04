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

        $stmt = $this->database->prepare("SELECT * FROM product ORDER BY name");
        $stmt->execute();

        while($product = $stmt->fetchObject()){
            $ret[] =  new \model\ProductModel($product->name, $product->price, $product->description, $product->id);
        }

        return $ret;
    }

    public function getProductById($id){
        $stmt = $this->database->prepare("SELECT * FROM product WHERE id = ?");
        $stmt->execute(array($id));

        if($product = $stmt->fetchObject()){
            return new \model\ProductModel($product->name, $product->price, $product->description);
        }

        throw new \Exception("Product not found");
    }
}