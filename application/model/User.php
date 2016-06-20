<?php

class model_user extends model {
    
    public $name;
    public $surname;
    public $username;
    public $email;
    public $password;
    public $status;

    function __construct($id = null) {
        parent::__construct($id);
        $this->setUp();
    }
    
    function setUp() {
        $this->name         = $this->result["name"];
        $this->surname      = $this->result["surname"];
        $this->username     = $this->result["username"];
        $this->email        = $this->result["email"];
        $this->password     = $this->result["password"];
        $this->status       = $this->result["status"];
    }
    
    function fullName() {
        return $this->name . " " . $this->surname;
    }
    
    function getCurrentUser() {
        return new model_user();
    }
}