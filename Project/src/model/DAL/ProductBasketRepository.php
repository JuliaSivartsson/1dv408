<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-02
 * Time: 21:35
 */

namespace model\dal;


class ProductBasketRepository
{
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

    public function addItem(\model\ProductModel $product)
    {
        $stmt = $this->database->prepare("INSERT INTO productBasket (productName, productPrice) VALUE (?,?)");
        $stmt->execute(array($product->getName(), $product->getPrice()));
        $product->setId($this->database->lastInsertId());
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