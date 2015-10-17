<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-15
 * Time: 10:46
 */

namespace model\dal;


use model\OrderModel;

class OrderRepository
{
    private $database;
    private static $customerId = 'customerid';

    public function __construct(){
        $this->dbTable = 'ordersedel';
        $connection = new DatabaseConnection();

        try{
            $this->database = $connection->SetupDatabase();
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function save(OrderModel $order)
    {

        try {

            $sql = "INSERT INTO $this->dbTable (" . self::$customerId . ") VALUE (?)";
            $params = array($order->getCustomerId());

            $query = $this->database->prepare($sql);

            $query->execute($params);
        }catch(\PDOException $e){
            return false;
        }
    }

    public function getLatestOrderByCustomerId($customerId){
        $stmt = $this->database->prepare("SELECT * FROM $this->dbTable WHERE ". self::$customerId ." = ? ORDER BY id DESC LIMIT 1 ");
        $stmt->execute(array($customerId));


        if($order = $stmt->fetchObject()){
            return new \model\OrderModel($order->customerid, $order->id);
        }

        throw new \Exception("Order not found");
    }
}