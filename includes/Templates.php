<?php

use ExceptionFactory as EF;

/**
 * Accesseur rapide aux fichiers de templates
 *
 * Les templates sont des fichiers .tpl.php situés dans le dossier templates
 * (configuré dans le fichier de configuration)
 * L'accès aux templates se fait via l'utilisation de clés
 * Exemple : $instance->header renverra le chemin du template header.tpl.php
 *
 * Les noms de templates préfixés par "js_" renverront le chemin du template js correspondant
 * Exemple : $instance->js_foo renverra le chemin du template js_foo.js
 *
 * @method string get($name) Renvoie le chemin du template demandé
 * @method string get_php($name) Renvoie le chemin du template php demandé (par défaut)
 * @method string get_js($name) Renvoie le chemin du template js demandé
 *
 */
class Templates{

    /**
     * Instance unique de la classe
     */
    private static $instance = null;

    private function __construct(){
        self::$instance = $this;
    }

    /**
     * Renvoie l'instance unique de la classe
     * @return Templates
     */
    public static function instance(): Templates{
        return self::$instance ?? new Templates();
    }

    /**
     * Renvoie le chemin du template demandé
     *
     * Si le nom du template commence par "js_", renvoie le chemin du template js correspondant
     * @see get_js()
     * Sinon, renvoie le chemin du template php correspondant
     * @see get_php()
     *
     * @param string $name Nom du template
     * @return string Chemin du template
     */
    public static function get($name): string{
        if(substr($name, 0, 3) == 'js_'){
            return self::get_js(substr($name, 3));
        }
        
        return self::get_php($name);
    }

    /**
     * Renvoie le chemin du template php demandé (par défaut)
     * @param string $name Nom du template
     * @return string Chemin du template
     * @throws Exception
     */
    public static function get_php($name){
        $cfg = Config::getInstance();
        $path = $cfg->path_templates.$name.'.tpl.php';
        if(file_exists($path)){
            return $path;
        }else{
            throw EF::file_not_found(
                $path,
                'To implement a new PHP template, create a file with the name "'.$name.'.tpl.php"'.
                'in the folder "'.$cfg->path_templates.'".',
            );
        }
    }

    /**
     * Renvoie le chemin du template js demandé
     * @param string $name Nom du template
     * @return string Chemin du template
     * @throws Exception
     */
    public static function get_js($name){
        $cfg = Config::getInstance();
        $path = $cfg->path_js.$name.'.js';
        if(file_exists($path)){
            return $path;
        }else{
            throw EF::file_not_found(
                $path,
                'To implement a new JS template, create a file with the name "'.$name.'.js"'.
                'in the folder "'.$cfg->path_js.'".',
            );
        }
    }

    /**
     * Permet l'accès rapide aux templates
     */
    public function __get($name){
        return self::get($name);
    }
}
