<?php
defined('APPLICATION_PATH') || define('APPLICATION_PATH', str_replace("\\","/", realpath(dirname(__FILE__) . '/../application')));
defined('PUBLIC_PATH') || define("PUBLIC_PATH", str_replace("\\", "/", realpath(dirname(__FILE__))));
defined('ADMIN_PATH') || define("ADMIN_PATH", str_replace("\\", "/", realpath(dirname(__FILE__) . '/../admin')));
define('ENV', "development"); //Enviroment can be switch between production and development

$config = parse_ini_file(ADMIN_PATH . "/config/config.ini", true);

ini_set("date.timezone", $config["time"]["timeZone"]);

function __autoload($name) {

    $arr = str_getcsv($name, '_');
    $newarr = str_getcsv($name, '/');
    
    $namspacedDir = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, __DIR__."/../".$name .  ".php");
    
    if(count($arr) > 1) {
        $str = "/";
        foreach($arr as $k=>$v) {
            if($k != count($arr) - 1) {
                $str .= $v . "/";
            } else {
                $str .= $v . ".php";
            }
        }
        require_once  APPLICATION_PATH . $str;
    } else {
        if(file_exists($namspacedDir)) {
            require  $namspacedDir;
        } else {
            require_once  APPLICATION_PATH . '/library/'. lcfirst($name) .  ".php";
        }
    }
}

class appException extends Exception{}
class userException extends Exception{}
class NotFoundException extends Exception{}
class ACLException extends Exception{}

$error = new controllers_error();

try {
    $boot = new bootstrap();
    $boot->setEnviroment();
    $boot->sessionStart();
    $boot->setApplication();
} catch(userException $e) {
    $error->generalError($e);
} catch(appException $e) {
    $error->generalError($e);
} catch(NotFoundException $e) {
    $error->notFoundError($e);
} catch(ACLException $e) {
    $error->accessError($e);
} catch(exception $e) {
    $error->generalError($e);
}








