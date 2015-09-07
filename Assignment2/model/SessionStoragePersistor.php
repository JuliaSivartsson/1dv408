<?php
namespace model;

class SessionStoragePersistor {

    private $sessionLocation;

    public function __construct($sessionLocation){
        $this->sessionLocation = $sessionLocation;

        //Make sure there is a session started
        assert(isset($_SESSION));
    }
}