<?php
class View
{
    public $setView = array();
    private $_doctype;
    
    public $headerFile = "/templates/head_loader.js";
    
    const HTML5     = '<!DOCTYPE html>';
    const STRICT    = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">';
    const FRAME     = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Frameset//EN" "http://www.w3.org/TR/html4/frameset.dtd">';
    const TRANS     = '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
    
    public function __construct($doctype) {
        $this->_doctype = $doctype;
    }
    
    function setView($array) {
        if(is_array($array) && !empty($array))
            $this->setView = $array;
    }
    
    function setPlugins() {
        $js = json_decode(file_get_contents(APPLICATION_PATH . $this->headerFile), true);
        $str = "";
        $type = $this->_doctype != "HTML5" ? ' type="text/javascript"'  : "";
        
        foreach($js as $k=>$v) {
            var_dump($v);
            foreach($v as $key=>$val) {
                foreach($val as $file) {
                    
                }
                $str .= '<script' . $type . ' src="/' . $key . "/" . $val . '.js"></script>';
            }
        }
        
        return $str;
    }
    
    function setStyles() {
        $css = functions::fileOnlyScan(PUBLIC_PATH . "/css", 0);
        $str = "";
        
        foreach($css as $v) {
            $str .= '<link rel="stylesheet" type="text/css" href="/css/' . $v . '">';
        }
        
        return $str;
    }
    
    function setTheme() {
        if(isset($this->setView["header"]["theme"])) {
            $str = "";
            $files = functions::fileOnlyScan(PUBLIC_PATH . "/themes/" . $this->setView["header"]["theme"], 0);
            if(is_array($files)) {
                foreach($files as $v) {
                    $str .= '<link rel="stylesheet" type="text/css" href="/themes/' . $this->setView["header"]["theme"] . "/" . $v . '" />';
                }
                return $str;
            }
        }
    }
    
    function setFavicon() {
        $fav = functions::fileOnlyScan(PUBLIC_PATH . "/images/icons/", 0);
        
        if(in_array("favicon.ico", $fav)) {
            return '<link rel="icon" href="/images/icons/favicon.ico" sizes="16x16" type="image/png" />';
        } else {
            return '';
        }
    } 
    
    function setTitle()
    {
        if(isset($this->setView["header"]["title"])) {
            return '<title>' . $this->setView["header"]["title"] . '</title>';
        } else {
            throw new appException("You have not set a title for this page", 103);
        }
    }
    
    function setJS() {
        $str = "";
        $type = "";
        
        if($this->_doctype != "HTML5") {
            $type = ' type="text/javascript"';
        }
        
        if(isset($this->setView["header"]["js"])) {
            foreach($this->setView["header"]["js"] as $v) {
                $str .= '<script' . $type . ' src="/scripts/' . $v . '"></script>';
            }
        }

        return $str;
    }
            
    function setHeader(){
        $header = "";
        
        if($this->setView["header"] !== false) {
            $header .= $this->setPlugins();
            $header .= $this->setJS();
            $header .= $this->setStyles();
            $header .= $this->setTheme();
            $header .= $this->setFavicon();
            $header .= $this->setTitle();
        }
        
        return $header;
    }
    
    function setDoctype(){
        if($this->_doctype == "HTML5") {
            $tag = self::HTML5;
        } else if($this->_doctype == "TRANS") {
            $tag = self::TRANS;
        } else if($this->_doctype == "STRICT") {
            $tag = self::STRICT;
        } else if($this->_doctype == "FRAME") {
            $tag = self::FRAME;
        } else {
            throw new appException("Did not recognise doctype \"" . $this->_doctype . "\" in view::setDoctype", 101);
        }
        return $tag;
    }
    
    function getPage() {
        return file_get_contents(APPLICATION_PATH . "/views/" . $this->setView["page"] . ".php");
    }
    
    function getTemplate() {
        return file_get_contents(APPLICATION_PATH . "/templates/" . $this->setView["template"] . ".php");
    }
    
    function setPage($buffer) {
        $ref = array("<!-- Header -->");
        if($this->setView["header"] !== false) {
            $val = array($this->setHeader());
        } else {
            $val = "";
        }
        $page =  (str_replace($ref, $val, $buffer));
        $page = explode("<!--Seperator-->", $page);
        return (str_replace('<!-- Content -->', $page[1], $page[0]));
    }
    
    public function render(){  
        //Create the index page
        ob_start(array($this, "setPage"));
        echo $this->setDoctype();
        require APPLICATION_PATH . "/templates/" . $this->setView["template"] . ".php";
        echo "<!--Seperator-->";
        require APPLICATION_PATH . "/views/" . $this->setView["page"] . ".php";
        ob_end_flush();
    }
    
    public function getView() {
        ob_start();
        require APPLICATION_PATH . "/views/" . $this->setView["page"] . ".php";
        $test = ob_get_contents();
        ob_end_clean();
        return $test;
    }
}