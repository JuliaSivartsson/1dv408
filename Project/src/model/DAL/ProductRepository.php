<?php

namespace model\dal;

class ProductRepository{

    private $database;
    private static $NameColumn = 'name';
    private static $quantityColumn = 'quantity';

    private $limit;
    private $page;
    private $total;
    private $query;

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
            $ret[] =  new \model\ProductModel($product->name, $product->price, $product->description, $product->quantity, $product->id);
        }

        return $ret;
    }

    public function getProductsPagination($page, $limit){

        $this->query = "SELECT * FROM  $this->dbTable ORDER BY ". self::$NameColumn ."";
        //$stmt->execute();

        $this->limit   = $limit;
        $this->page    = $page;

        if ( $this->limit == 'all' ) {
            $limitQuery      = $this->query;
        } else {
            $limitQuery = $this->query . " LIMIT " . ( ( $this->page - 1 ) * $this->limit ) . ", $this->limit";

        }

        $rs = $this->database->prepare($limitQuery);
        $rs->execute();

        while($product = $rs->fetchObject()){
            $ret[] =  new \model\ProductModel($product->name, $product->price, $product->description, $product->quantity, $product->id);
        }

        $result         = new \stdClass();
        $result->page   = $this->page;
        $result->limit  = $this->limit;
        $result->total  = $this->total;
        $result->data   = $ret;

        return $result;
    }

    public function getProductById($id){
        $stmt = $this->database->prepare("SELECT * FROM  $this->dbTable WHERE id = ?");
        $stmt->execute(array($id));

        if($product = $stmt->fetchObject()){
            return new \model\ProductModel($product->name, $product->price, $product->description, $product->quantity, $product->id);
        }

        throw new \Exception("Product not found");
    }

    public function reduceQuantity($id, $numberToReduce){

        $stmt = $this->database->prepare("UPDATE $this->dbTable SET ". self::$quantityColumn."= ? WHERE id=$id");

        $stmt->execute(array($numberToReduce));
    }

    public function getProductByName($name){
        $stmt = $this->database->prepare("SELECT * FROM $this->dbTable WHERE ". self::$NameColumn ." = ?");
        $stmt->execute(array($name));

        if($product = $stmt->fetchObject()){
            return new \model\ProductModel($product->name, $product->price, $product->description, $product->quantity, $product->id);
        }

        throw new \Exception("Product not found");
    }
}