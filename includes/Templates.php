<?php

class Templates{

    private static $instance = null;

    private function __construct(){
        self::$instance = $this;
    }

    public static function instance(){
        return self::$instance ?? new Templates();
    }

    static function get($name){
        $cfg = Config::getInstance();
        $path = $cfg->path_templates.$name.'.tpl.php';
        if(file_exists($path)){
            return $path;
        }else{
            throw new Exception('In '.__CLASS__.'::'.__FUNCTION__.' : template file "'.$path.'" not found. Please verify template folder in config file or that the template file exists.');
        }
    }

    function __get($name){
        return self::get($name);
    }
}