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
        $path = $cfg->root . '/includes/' . $className . '.php';
        var_dump($path);
        if (file_exists($path)) {
            require_once $path;
        }
    }
}

Autoload::init();