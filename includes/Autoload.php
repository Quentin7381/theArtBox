<?php

class Autoload {

    private static $instance = null;

    private function __construct() {
        spl_autoload_register([$this, 'autoload']);
    }

    public static function init() {
        if (is_null(self::$instance)) {
            self::$instance = new Autoload();
        }
    }

    public function autoload($className) {
        $cfg = Config::getInstance();
        $path = $cfg->path_includes . $className . '.php';
        if (file_exists($path)) {
            require_once $path;
        }
    }
}

Autoload::init();