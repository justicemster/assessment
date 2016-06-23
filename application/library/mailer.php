<?php

class mailer extends phpmailer {

    function __construct() {
        $parser = ADMIN_PATH . "/config/config.ini";
        $config = parse_ini_file($parser, true);

        $this->isSMTP();
        $this->Host         = $config["mail"]["host"];
        $this->Port         = $config["mail"]["port"];
        $this->SMTPAuth     = $config["mail"]["SMTPauth"];
        $this->Username     = $config["mail"]["username"];
        $this->Password     = $config["mail"]["password"];
    }

}