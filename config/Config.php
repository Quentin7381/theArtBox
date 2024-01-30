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

    public function override($key, $value){
        $this->config[$key] = $value;
    }
}

$cfg = Config::getInstance();

// Calcul du chemin du serveur
// Note : theArtbox est le nom du dossier dans lequel se trouve le projet
$calledUrl = $_SERVER['REQUEST_URI'] ?? '';
$regex = '/(^.*\/theArtbox\/)/';
preg_match($regex, $calledUrl, $matches);
$cfg->url_root = $matches[0] ?? '/';

$root = dirname(__DIR__);
$root = str_replace($cfg->url_root, '', $root);
$cfg->path_root = $root.'/';
$cfg->path_templates = $cfg->path_root.'templates/';
$cfg->path_assets = $cfg->path_root.'assets/';
$cfg->path_img = $cfg->path_assets.'img/';
$cfg->path_css = $cfg->path_assets.'css/';
$cfg->path_js = $cfg->path_assets.'js/';
$cfg->path_includes = $cfg->path_root.'includes/';

$cfg->url_assets = $cfg->url_root.'assets/';
$cfg->url_img = $cfg->url_assets.'img/';
$cfg->url_css = $cfg->url_assets.'css/';
$cfg->url_js = $cfg->url_assets.'js/';
$cfg->url_admin = $cfg->url_root.'admin/';

$cfg->db_host = 'localhost';
$cfg->db_user = 'root';
$cfg->db_pass = '';
$cfg->db_name = 'theartbox';
$cfg->db_throwExceptions = true;
$cfg->db_defaultLimit = 10;

$cfg->autoload = $cfg->path_root.'/includes/Autoload.php';
require_once $cfg->autoload;

$db = BDD::getInstance();