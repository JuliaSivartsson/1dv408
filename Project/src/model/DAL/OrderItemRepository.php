<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-15
 * Time: 10:46
 */

namespace model\dal;

use \model\OrderItemModel;

class OrderItemRepository
{
    private $database;
    private static $productIdColumn = 'productid';
    private static $orderIdColumn = 'orderid';

    public function __construct(){
        $this->dbTable = 'orderitem';
        $connection = new DatabaseConnection();

        try{
            $this->database = $connection->SetupDatabase();
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function save(OrderItemModel $orderItem){

        try {

            $sql = "INSERT INTO $this->dbTable (" . self::$orderIdColumn . "," . self::$productIdColumn . ") VALUE (?,?)";
            $params = array($orderItem->getOrderId(), $orderItem->getProductId());
            $query = $this->database->prepare($sql);
            $query->execute($params);
        }catch(\PDOException $e){
            return false;
        }
    }


    public function getAllOrderItemsWithOrderId($orderId){
        $stmt = $this->database->prepare("SELECT * FROM $this->dbTable WHERE ". self::$orderIdColumn ." = ? ORDER BY id");
        $stmt->execute(array($orderId));


        while($orderItem = $stmt->fetchObject()){
            $ret[] =  new \model\OrderItemModel($orderItem->orderid, $orderItem->productid, $orderItem->id);
        }
        return $ret;

    }

}