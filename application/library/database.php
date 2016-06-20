<?php

class database extends mysqli {
    
    public $dbError = false;

    function __construct($host = null, $username = null, $password = null, $dbname = null)
    {
        $ini = parse_ini_file(ADMIN_PATH . "/config/config.ini", true);

        $host       = isset($host) ? $host : $ini["database"]["dbhost"];
        $username   = isset($username) ? $username : $ini["database"]["dbusername"];
        $password   = isset($password) ? $password : $ini["database"]["dbpassword"];
        $dbname     = isset($dbname) ? $dbname : $ini["database"]["dbname"];
        
        if($dbname !== "false") {
            parent::__construct($host, $username, $password, $dbname);

            if(!$this->set_charset($ini["database"]["charset"])) {
                throw new appException($this->error);
            }

            if (mysqli_connect_error()) {
                throw new appException("Connect Error (" . mysqli_connect_errno() . ") " . mysqli_connect_error(), 101);
            }
        } else {
            return false;
        }
    }
}