<?php

class controllerException extends Exception{}

class Controller
{
    public $view;
    public $subactions;
    
    protected $_db;
    protected $_currentUser;
    protected $_model;
    
    function __construct() {   
        $this->view = new view("HTML5");     
        

//        $this->_db = new database();

        if(file_exists(APPLICATION_PATH . "/model/user.php")) {
            $this->_currentUser = new model_user();
            $this->_currentUser->getCurrentUser();
        }
    }
}

