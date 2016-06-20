<?php

class controllers_index extends Controller {

    function __construct() {
        parent::__construct();
    }
    
    function indexAction() {
        $view = new view("HTML5");
        $view->setView(array(
            "page"=>"index/index",
            "template"=>"index",
            "header"=>array(
                "title"=>"Hello Assessee",
                "theme"=>"frontend",
                "js"=>array()
            )
        ));

        $view->render();
    }
}