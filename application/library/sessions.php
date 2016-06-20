<?php

class sessions {
    
    private $_db;
    
    function __construct() {
        $this->_db = new database();
    }
    
    function open($save_path, $session_name) 
    {
        return true;
    }
    
    function close()
    {
        $this->_db->close();
        return true;
    }
    
    function read($session_id)
    {
        $result = $this->_db->fetch_all_stmt("SELECT sessionData FROM sessions WHERE sessionID=?", "s", array($session_id), true);
        
        if(isset($result["sessionData"])) {
            return $result["sessionData"];
        } else {
            return "";
        }
        
    }
    
    function write($session_id, $session_data)
    {
        if($this->_db->rows("SELECT * FROM sessions WHERE sessionID=?", "s", array($session_id)) == 0) {
            if($this->_db->insert(array("sessionID"=>$session_id, "sessionData"=>$session_data, "sessionLastAccessed"=>date("d-m-Y H:i")), "sessions") === true) {
                return true;
            } else {
                return false;
            }
        } else {
            if($this->_db->update(array("sessionData"=>$session_data, "sessionLastAccessed"=>date("d-m-Y H:i")), "sessions", "sessionID", $session_id) === true) {
                return true;
            } else {
                return false;
            }
        }
    }
    
    function destroy($session_id)
    {
        if($this->_db->delete("DELETE FROM sessions WHERE sessionID=?", "s", array($session_id)) === true) {
            functions::unsetCookie();
            return true;
        } else {
            return false;
        }
    }
    
    function gc($max_lifetime) {
        $date = new DateTime(date("Y-m-d"));
        $date->sub(new DateInterval("P2H"));
        
        $this->_db->fetch_all_stmt("DELETE FROM sessions WHERE sessionLastAccessed <= ?", "s", array($date->format("Y-m-d")));
        return true;
    }
}