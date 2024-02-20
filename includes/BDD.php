<?php

/**
 * Classe de connexion à la base de données
 *
 * Possède le même fonctionnement que PDO, mais configure la connexion à la base de données automatiquement
 * La configuration de la base de données se fait via le fichier config/Config.php
 */
class BDD extends PDO {

    /**
     * Instance singleton de BDD
     *
     * @var BDD
     */
    private static $instance = null;

    /**
     * Crée et configure une instance de PDO pour la connexion à la base de données
    */
    private function __construct() {
        $cfg = Config::getInstance();
        $dsn = 'mysql:host=' . $cfg->db_host . ';dbname=' . $cfg->db_name . ';charset=utf8';
        parent::__construct($dsn, $cfg->db_user, $cfg->db_pass);
        if($cfg->db_throwExceptions){
            $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
    }

    /**
     * Renvoie l'instance singleton de BDD
     *
     * @return BDD
     */
    public static function getInstance(): BDD{
        if (is_null(self::$instance)) {
            self::$instance = new BDD();
        }
        return self::$instance;
    }
}
