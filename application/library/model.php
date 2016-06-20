<?php

class model  {
    
    public $result;
    public $orderBy;
    
    protected $_db;
    protected $_table;
    protected $_ref;
    protected $_controller;
    protected $_action;
    
    function __construct($id = null) {
        $this->_db = new database();
        if(isset($id)) {
            $sql = "SELECT * FROM " . $this->_table . " WHERE " . $this->_ref . "=?";
            $result = $this->_db->fetch_all_stmt($sql, "i", array($id), true);
            if(!empty($result)) {
                $this->result = $result;
            }
        } 
    }
    
    function setUp() {
        return false;
    }

    function getRows() {   
        return $this->_db->fetch_all_stmt("SELECT " . $this->_ref .  " FROM " . $this->_table . " ORDER BY " . $this->_ref);
    }
    
    function updateForm($array, request $request) {
        $array["uniqueID"] = $request->uniqueID;
        $array["form"] = array_values($array["form"]);
        return json_encode($array);
    }
    
    protected function deleteMsg($dberrorno) {
        if($dberrorno == 1451) {
            return "You cannot delete this item as it is already referenced in the database";
        } else {
            return "Your request was unsuccessful";
        }
    }
    
    function createForm(request $request) {
        
    }
    
    function create(request $request) {
        
    }
    
    function read(request $request) {
        
    }
    
    function update(request $request) {
        
    }
    
    function delete(request $request) {
        
    }

    function formRender($arr) {
        
        if(isset($arr["path"])) {
            $form = json_decode(file_get_contents(APPLICATION_PATH . $arr["path"]), true);
        } else {
            throw new appException("library/controller::formRender key \"path\" not set");
        }
        
        //Check to see if there are values to be entered
        if(isset($arr["values"])) {
            foreach($form as $k=>$v) {
                if($v["element"] != "table") {
                    if(isset($v["name"]) && isset($arr["values"][$v["name"]])) {
                        $form[$k]["value"] = $arr["values"][$v["name"]];
                    }
                } else {
                    foreach($v["rows"] as $rowKey=>$row) {
                        foreach($row as $colKey=>$col) {
                            if(isset($col["name"]) && isset($arr["values"][$col["name"]])) {
                                $form[$k]["rows"][$rowKey][$colKey]["value"] = $arr["values"][$col["name"]];
                            }
                        }
                    }
                }
            }
        }
        
        $array["form"] = $form;

        if(isset($arr["list"])) {
            $user = new model_user();
            $user->getCurrentUser();
            
            $configuration = new model_configuration();

            //Set this to disable the editing function of certain lists
            $array["editStatus"] = in_array($user->roleID, json_decode($configuration->formSelectEditing)) ? 1 : 0;
            
            //Add the list
            $array["list"] = $arr["list"];
        }
        
        return $array;
    }
    
    function getController() {
        if(!isset($this->_controller)) {
            throw new appException("Must set the controller property to continue");
        } else {
            return $this->_controller;
        }
    }
    
    function getAction() {
        if(!isset($this->_action)) {
            throw new appException("Must set the action property to continue");
        } else {
            return $this->_action;
        }
    }
    
    function dateSetup($date) {
        if(isset($date)) {
            return new DateTime($date);
        } else {
            return NULL;
        }
    } 
    
    //This will help extract model specific information from data sent by the client
    //where model information is mixed to make the user experience easier and quicker
    function extractor(array $array, $prefix, $regex = null) {
        $newarray = array();
        
        //Make it simple by allowing user to just use a prefix
        if(is_null($regex)) {
            $prefix = '/'.$prefix.'/';
        } else { //Make it flexiable by adding the ability to prefix a regular expression
            $prefix = $regex;
        }
        
        //Remove select parts of array and return new array
        foreach($array as $k=>$v) {
            if(preg_match($prefix, $k)) {
                $newarray[$k] = $v;
            }
        }
        
        return $newarray;
    }
}
