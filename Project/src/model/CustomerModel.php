<?php
/**
 * Created by PhpStorm.
 * User: julia
 * Date: 2015-10-14
 * Time: 10:20
 */

namespace model;

class InvalidSSNException extends \Exception{}

class InvalidFirstNameException extends \Exception{}

class InvalidLastNameException extends \Exception{}

class InvalidEmailException extends \Exception{}

class InvalidAddressException extends \Exception{}

class InvalidPostalCodeException extends \Exception{}

class InvalidCityException extends \Exception{}

class CustomerModel
{
    private $id;
    private $ssn;
    private $firstName;
    private $lastName;
    private $email;


    public function __construct($ssn, $firstName, $lastName, $email, $id = 0){
        $this->ssn = $ssn;
        $this->firstName = trim($firstName);
        $this->lastName = trim($lastName);
        $this->email = $email;
        $this->id = $id;


        if (!preg_match("/^((18|19|20)?[0-9]{2})(0[1-9]|1[012])(0[1-9]|[12][0-9]|3[01])(-)?[0-9pPtTfF][0-9]{3}$/", $this->ssn)) {
            throw new InvalidSSNException();
        }
        if (mb_strlen($this->firstName) < 3 || $this->firstName !== strip_tags($this->firstName)) {
            throw new InvalidFirstNameException();
        }
        if (mb_strlen($this->lastName) < 3 || $this->lastName !== strip_tags($this->lastName)) {
            throw new InvalidLastNameException();
        }
        //TODO get regex for email
        if((!filter_var($this->email, FILTER_VALIDATE_EMAIL))){
            throw new InvalidEmailException();
        }
    }

    public function getId(){
        return $this->id;
    }

    public function getSSN(){
        return $this->ssn;
    }

    public function getFirstName(){
        return $this->firstName;
    }

    public function getLastName(){
        return $this->lastName;
    }

    public function getEmail(){
        return $this->email;
    }



}