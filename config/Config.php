<?php

/**
 * Gère la configuration de l'application par association de clés/valeurs
 *
 * @method Config getInstance() Renvoie l'instance singleton de Config
 * @method mixed __get(string $key) Renvoie la valeur associée à la clé $key
 * @method void __set(string $key, mixed $value) Associe la valeur $value à la clé $key
 * @method void override(string $key, mixed $value)
 *          Associe la valeur $value à la clé $key, même si la clé est déjà définie
 */
class Config {

    /**
     * Instance singleton de Config
     *
     * @var Config
     */
    private static $instance = null;

    /**
     * Tableau associatif contenant les clés/valeurs de configuration
     *
     * @var array
     */
    private $config = [];

    private function __construct(){
        self::$instance = $this;
    }

    /**
     * Renvoie l'instance singleton de Config
     *
     * @return Config
     */
    public static function getInstance(): Config{
        if(is_null(self::$instance)){
            self::$instance = new Config();
        }
        return self::$instance;
    }

    /**
     * Renvoie la valeur associée à la clé $key
     *
     * @param string $key Clé de configuration
     * @return mixed Valeur associée à la clé $key
     * @throws Exception Si la clé $key n'est pas définie
     * @throws Exception Si la clé $key n'est pas une chaîne de caractères ou un entier
     */
    public function __get($key){
        if(!is_string($key) && !is_integer($key)) {
            throw new Exception(
                'Dans '.__CLASS__.', '.__FUNCTION__.': '.
                'la clé doit être une chaîne de caractères ou un entier, '.gettype($key).' donné'
            );
        }
        if(!isset($this->config[$key])) {
            throw new Exception("Dans ".__CLASS__.', '.__FUNCTION__.': la clé '.$key.' n\'est pas définie');
        }
        return $this->config[$key];
    }

    /**
     * Associe la valeur $value à la clé $key
     *
     * @param string $key Clé de configuration
     * @param mixed $value Valeur à associer à la clé $key
     * @warning Si la clé $key est déjà définie, une alerte sera émise
     * @throws Exception Si la clé $key n'est pas une chaîne de caractères ou un entier
     */
    public function __set($key, $value){
        if(!is_string($key) && !is_integer($key)) {
        throw new Exception(
            'Dans '.__CLASS__.', '.__FUNCTION__.': '.
            'la clé doit être une chaîne de caractères ou un entier, '.gettype($key).' donné'
        );
    }
        if(isset($this->config[$key])){
            user_error(
                'Dans '.__CLASS__.', '.__FUNCTION__.': la valeur de la clé "'.$key.'" est déjà'.
                'définie et ne sera pas changée',
                E_USER_WARNING
            );
        }
        $this->config[$key] = $value;
    }

    /**
     * Associe la valeur $value à la clé $key, même si la clé est déjà définie
     *
     * @param string $key Clé de configuration
     * @param mixed $value Valeur à associer à la clé $key
     * @throws Exception Si la clé $key n'est pas une chaîne de caractères ou un entier
     */
    public function override($key, $value){
        if(!is_string($key) && !is_integer($key)) {
            throw new Exception(
                'Dans '.__CLASS__.', '.__FUNCTION__.': '.
                'la clé doit être une chaîne de caractères ou un entier, '.gettype($key).' donné'
            );
        }
        $this->config[$key] = $value;
    }
}

$cfg = Config::getInstance();

// ----- URL -----
// URL racine du serveur:
// On récupère l'URL appelée jusqu'à theArtbox/ et on en déduit le chemin du serveur
// Note : theArtbox est le nom du dossier dans lequel se trouve le projet
$calledUrl = $_SERVER['REQUEST_URI'] ?? '';
$regex = '/(^.*\/theArtbox\/)/';
preg_match($regex, $calledUrl, $matches);
$cfg->url_root = $matches[0] ?? '/';

// URL des fichiers assets
$cfg->url_assets = $cfg->url_root.'assets/';

// URL des images
$cfg->url_img = $cfg->url_assets.'img/';

// URL des fichiers CSS
$cfg->url_css = $cfg->url_assets.'css/';

// URL des fichiers JS
$cfg->url_js = $cfg->url_assets.'js/';

// URL des fichiers PHP
$cfg->url_admin = $cfg->url_root.'admin/';

// ----- CHEMINS -----
// Chemin racine du serveur
$root = dirname(__DIR__);
$root = str_replace($cfg->url_root, '', $root);
$cfg->path_root = $root.'/';

// Chemins des fichiers templates
$cfg->path_templates = $cfg->path_root.'templates/';

// Chemins des fichiers assets
$cfg->path_assets = $cfg->path_root.'assets/';

// Chemins des images
$cfg->path_img = $cfg->path_assets.'img/';

// Chemins des fichiers CSS
$cfg->path_css = $cfg->path_assets.'css/';

// Chemins des fichiers JS
$cfg->path_js = $cfg->path_assets.'js/';

// Chemins des fichiers PHP
$cfg->path_includes = $cfg->path_root.'includes/';

// Récupération de l'autoloader
$cfg->autoload = $cfg->path_root.'/includes/Autoload.php';
require_once $cfg->autoload;

// ------ BASE DE DONNEES ------
// Hôte de la base de données
$cfg->db_host = 'localhost';

// Nom d'utilisateur de la base de données
$cfg->db_user = 'root';

// Mot de passe de la base de données
$cfg->db_pass = '';

// Nom de la base de données
$cfg->db_name = 'theartbox';

// Activer ou non les exceptions pour les erreurs de base de données
$cfg->db_throwExceptions = true;

// Limite par défaut de nombre de résultats pour les requêtes SQL
$cfg->db_defaultLimit = 10;

// Récupération de la base de données
$db = BDD::getInstance();
