<?php

class Config {

    private static $instance = null;
    private $config = [];

    private function __construct(){
        self::$instance = $this;
    }

    public static function getInstance(){
        if(is_null(self::$instance)){
            self::$instance = new Config();
        }
        return self::$instance;
    }

    public function __get($key){
        if(!isset($this->config[$key])) throw new Exception("Dans ".__CLASS__.', '.__FUNCTION__.': la clé '.$key.' n\'est pas définie');
        return $this->config[$key];
    }

    public function __set($key, $value){
        if(isset($this->config[$key])){
            user_error("Dans ".__CLASS__.', '.__FUNCTION__.': la valeur de la clé "'.$key.'" est déjà définie et ne sera pas changée', E_USER_WARNING);
        }
        $this->config[$key] = $value;
    }
}

$cfg = Config::getInstance();

// Calcul du chemin du serveur
// Note : theArtbox est le nom du dossier dans lequel se trouve le projet
$calledUrl = $_SERVER['REQUEST_URI'];
$regex = '/(^.*\/theArtbox\/)/';
preg_match($regex, $calledUrl, $matches);
$cfg->urlRoot = $matches[0] ?? '/';

$root = dirname(__DIR__);
$root = str_replace('\\', '/', $root);
$root = str_replace($cfg->urlRoot, '', $root);
$cfg->root = $root;