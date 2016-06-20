<?php

class Bootstrap {
    
    private $_url;
    private $_ajax;
    public $_currentUser;
    
    function __construct() {
        $uri = strip_tags($_SERVER["REQUEST_URI"]);
        $xml = isset($_SERVER["HTTP_X_REQUESTED_WITH"]) ? (strip_tags(@$_SERVER["HTTP_X_REQUESTED_WITH"])) : NULL;
        $this->_url = isset($uri) ? $uri : null;
        $this->_ajax = isset($xml) ? (bool) $xml : null;
    }

    
    function sessionStart() {
        $handler = new sessions();
        session_set_save_handler(
            array($handler, 'open'),
            array($handler, 'close'),
            array($handler, 'read'),
            array($handler, 'write'),
            array($handler, 'destroy'),
            array($handler, 'gc')
            );
        //Start the session and regenerate the id
        register_shutdown_function('session_write_close');
        session_start();
    }
    
    protected function trimedURL() {
        if(isset($this->_url)) {
            return trim($this->_url, '/');
        } else {
            throw new bootstrapException("The url for the bootstrap could not be set");
        }
    }
    
    protected function removeQueryString() {
        if($this->trimedURL() !== null) {
            //Remove the query string
            $pos = strpos($this->trimedURL(), "?", 0);
            $pos = $pos !== false ? substr($this->trimedURL(), 0, $pos) : $this->trimedURL();
            
            return $pos;
        }
    }
    
    protected function urlArray() {
        //Split url by the back slash
       
        return explode('/', $this->removeQueryString());
    }
    
    protected function camelCaseElement($array) {
        //Turn hyphenated string into camel case 
        foreach($array as $k=>$v) {
            if($k == 0) {
                $str = $v;
            } else {
                $str .= ucfirst($v);
            }
        }
        return $str;
    }
    
    protected function setUrl() {
        $url = $this->urlArray();        
        
        if(!isset($url[0]) || $url[0] == "") {
            $url[0] = "index";
        } 
        
        if (!isset($url[1]) || $url[1] == "") {
            $url[1] = "index";
        }
        return $url;
    }
    
    function getURL($del = null) {
        $url = $this->setUrl();
       
        foreach($url as $k=>$v) {
            if($new = str_getcsv($v, "-")) {
                $url[$k] = $this->camelCaseElement($new);
            }  else {
                $url[$k] = $v;
            }
        }
        return $url;
    }
    
    protected function getController() {
        
        $url = $this->getURL();
        
        return $url[0];
    }
    
    protected function getAction() {
        $url = $this->getURL();
        return $url[1] . "Action";
    }
    
    protected function getAjaxAction() {
        $url = $this->getURL();
        return $url[1] . "Ajax";
    }
    
    protected function getSubActions() {
        return array_slice($this->setUrl(), 2);
    }
    
    protected function updateLog() {
        $request = new request();
        $user = new model_user();
        $user->getCurrentUser();
        $request->logRequest($user->ID);
    }

    public function setApplication()
    {
        $path = APPLICATION_PATH . "/controllers/" . $this->getController() . ".php";
        
        if(file_exists($path)) {
            
            $controller = "controllers_" . $this->getController();
            $controller = new $controller();
            
            if(!isset($controller->singlePage)) {
                if($this->_ajax === true) {
                    if(method_exists($controller, $this->getAjaxAction())) {
                        $controller->{$this->getAjaxAction()}(@$this->getSubActions());
                    } else {
                        echo http_response_code(404);
                    }
                } else {
                    if(method_exists($controller, $this->getAction())) {
                        $controller->{$this->getAction()}(@$this->getSubActions());
                        
                        //For each non ajax request the logRequest is 
                        //updated keeping the user logged in
                        if(isset($_SESSION["auth"])) {
                            $this->updateLog();
                        }
                    } else {
                        header("location: /");
                    }
                }
            } else {
                
                $index = new controllers_index();
                return $index->indexAction();
            }
        } else {
             header("location: /");
        }
    }

    public function setEnviroment()
    {
        $config     = ADMIN_PATH . "/config/config.ini";

        if(file_exists($config)) {
            $ini    = parse_ini_file($config, true);
            foreach($ini[ENV] as $k=>$v) {;
                ini_set($k, $v);
                if($k == "error_log") {
                    ini_set($k, ADMIN_PATH . $v);
                }
            }
        }  else {
            throw new appException("File config.ini does not exist", 805);
        }
    }
}
?>
