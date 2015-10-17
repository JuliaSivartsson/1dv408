<?php
    /**
     * Created by PhpStorm.
     * User: julia
     * Date: 2015-10-15
     * Time: 10:46
     */

namespace model\dal;


    use model\CustomerModel;

class CustomerRepository
{
    private $database;
    private static $idColumn = 'id';
    private static $SSNColumn = 'ssn';
    private static $FirstNameColumn = 'firstname';
    private static $LastNameColumn = 'lastname';
    private static $emailColumn = 'email';

    public function __construct(){
        $this->dbTable = 'customer';
        $connection = new DatabaseConnection();

        try{
            $this->database = $connection->SetupDatabase();
        }catch(\Exception $e){
            throw $e;
        }
    }

    public function save(CustomerModel $customer){

        try {

            $sql = "INSERT INTO $this->dbTable (" . self::$SSNColumn . "," . self::$FirstNameColumn . "," . self::$LastNameColumn . "," . self::$emailColumn . ") VALUE (?,?,?,?)";
            $params = array($customer->getSSN(), $customer->getFirstName(), $customer->getLastName(), $customer->getEmail());

            $query = $this->database->prepare($sql);

            $query->execute($params);
        }catch(\PDOException $e){
            return false;
        }
    }

    public function getCustomerBySsn($ssn){

        $stmt = $this->database->prepare("SELECT * FROM $this->dbTable WHERE ". self::$SSNColumn ." = ?");
        $stmt->execute(array($ssn));


        if($customer = $stmt->fetchObject()){
            return new \model\CustomerModel($customer->ssn, $customer->firstname, $customer->lastname, $customer->email, $customer->id);
        }

        throw new \Exception("Customer not found");
    }

    public function getCustomerById($id){

        $stmt = $this->database->prepare("SELECT * FROM $this->dbTable WHERE ". self::$idColumn ." = ?");
        $stmt->execute(array($id));


        if($customer = $stmt->fetchObject()){
            return new \model\CustomerModel($customer->ssn, $customer->firstname, $customer->lastname, $customer->email, $customer->id);
        }

        throw new \Exception("Customer not found");
    }

    public function getLatestCustomer(){

        $stmt = $this->database->prepare("SELECT * FROM $this->dbTable ORDER BY id DESC LIMIT 1 ");
        $stmt->execute();

        if($customer = $stmt->fetchObject()){
            return new \model\CustomerModel($customer->ssn, $customer->firstname, $customer->lastname, $customer->email, $customer->id);
        }
    }

}