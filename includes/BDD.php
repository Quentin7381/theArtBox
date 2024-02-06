<?php

class BDD extends PDO {

    private static $instance = null;

    private function __construct() {
        $cfg = Config::getInstance();
        $dsn = 'mysql:host=' . $cfg->db_host . ';dbname=' . $cfg->db_name . ';charset=utf8';
        parent::__construct($dsn, $cfg->db_user, $cfg->db_pass);
        if($cfg->db_throwExceptions){
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    public static function getInstance() {
        if (is_null(self::$instance)) {
            self::$instance = new BDD();
        }
        return self::$instance;
    }
}