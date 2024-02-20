<?php

/**
 * Charge automatiquement les fichiers PHP contenant les classes requises
 */
class Autoload {

    /**
     * Instance singleton de Autoload
     *
     * @var Autoload
     */
    private static $instance = null;

    private function __construct() {
        spl_autoload_register([$this, 'load']);
    }

    /**
     * Crée l'instance de la classe Autoload et l'enregistre comme autoloader
     *
     * Initialiser plusieurs fois la classe n'a aucun effet
     */
    public static function init(): void{
        if (is_null(self::$instance)) {
            self::$instance = new Autoload();
        }
    }

    /**
     * Charge le fichier contenant la classe demandée
     *
     * Le fichier doit être dans le dossier includes (configurable dans config/Config.php)
     * Il doit avoir le même nom que la classe
     *
     * @param string $className Nom de la classe
     */
    public function load($className): void{
        $cfg = Config::getInstance();
        $path = $cfg->path_includes . $className . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
}

// Initialisation de l'autoloader
Autoload::init();
