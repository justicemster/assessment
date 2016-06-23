<?php

class database extends mysqli {
    
    public $dbError = false;

    function __construct($host = null, $username = null, $password = null, $dbname = null) {
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
    
    public function fetch_all_stmt($sql, $types = null, $params = null, $oneDimension = null) {
        $arr = array();
        
        if(!is_array($params) && $params != null) {
            throw new appException("fetch_all_stmt expects 3rd argument to be an array");
        }
        
        # create a prepared statement
        $stmt = $this->prepare($sql);

        if($stmt) {
            if($types&&$params) {
                $bind_names[] = $types;
                for ($i=0; $i<count($params);$i++) {
                    $bind_names[] = &$params[$i];

                }
                call_user_func_array(array($stmt,'bind_param'),$bind_names);
            }

            # execute query
            if(!$stmt->execute()) {
                throw new appException($this->error, $this->errno);
            }

            # these lines of code below return multi-dimentional/ nested array, similar to mysqli::fetch_all()
            $stmt->store_result();


            if($stmt->num_rows > 0) {
                $variables = array();
                $data = array();
                $meta = $stmt->result_metadata();

                while($field = $meta->fetch_field())
                    $variables[] = &$data[$field->name]; // pass by reference

                call_user_func_array(array($stmt, 'bind_result'), $variables);
                $array = null;
                $i=0;
                while($stmt->fetch()) {
                    $array[$i] = array();
                    foreach($data as $k=>$v)
                        $array[$i][$k] = $v;
                    $i++;
                } 
                
            
            } else {
                $array = array();
            }
            
            /*
             * Change the result to a one dimensional array
             */
            if($oneDimension === true) {
                foreach($array as $k => $v) {
                    foreach($v as $key=>$val) {
                        $arr[$key] = $val;
                    }
                }
                return $arr;
            } else {
                return $array;
            }

            # close statement
            $stmt->close();
        } else {
            $this->dbError = $this->error;
            return false;
        }
    }
    
    public function rows($query, $types = null, $params = null) {
         if($stmt = $this->prepare($query)) {
            if($types&&$params) {

                $bind_names[] = $types;
                for ($i=0; $i<count($params);$i++) {
                    $bind_names[] = &$params[$i];

                }

                call_user_func_array(array($stmt,'bind_param'),$bind_names);
            }
            
            if($stmt->execute()) {
                $stmt->store_result();
                $stmt->fetch();
                return $stmt->num_rows;
            } else {
                return false;
            }
            
        } else {
            return false;
        }
    }
}