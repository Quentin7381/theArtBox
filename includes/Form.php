<?php

class Form {

    protected $cfg = null;
    protected $oeuvre = null;
    protected $errors = [];
    protected $data = [];
    protected $results = [];
    protected $fields = [
        'titre' =>'text',
        'auteur' =>'text',
        'description' =>'textarea',
        'url_image' =>'file',
        'id' =>'hidden',
    ];

    protected $method = 'POST';
    protected $enctype = 'multipart/form-data';
    protected $action = '';

    public function __construct($options =[], $data = []) {
        global $cfg;
        $this->cfg = $cfg;
        $this->data = $data;
        $this->oeuvre = $options['oeuvre'] ?? new Oeuvre();
    }

    public function __get($key) {
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }
        return null;
    }

    public function __set($key, $value) {
        if(in_array($key, array_keys($this->fields))) {
            $fieldType = $this->fields[$key];
            $methodName = 'set_'.$key;
            if(method_exists($this, $methodName)) {
                $this->oeuvre->$methodName($value);
            } else return false;

        } else return false;
    }

    function set_text($key, $value) {
        $value = trim($value);
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        $this->data[$key] = $value;
    }

    function set_textarea($key, $value) {
        $value = trim($value);
        $value = filter_var($value, FILTER_SANITIZE_STRING);
        $this->data[$key] = $value;
    }

    function set_file($key, $value) {
        if($value['error'] === 0) {
            $image = $value;
            $ext = pathinfo($image['name'], PATHINFO_EXTENSION);
            $name = uniqid().'.'.$ext;
            move_uploaded_file($image['tmp_name'], $this->cfg->path_root.'/img/'.$name);
            $this->data[$key] = $name;
        } else {
            $this->errors[$key] = 'Erreur lors de l\'upload de l\'image';
        }
    }

    function submit(){
        $methodName = 'submit_'.$this->action;
        if(method_exists($this, $methodName)) {
            return $this->$methodName();
        }
    }

    function submit_search(){
        $this->results = Oeuvre::search($this->data[]);
        return true;
    }

    function submit_add(){
        foreach($this->data as $key => $value){
            $this->oeuvre->$key = $value;
        }

        if($this->oeuvre->save()){
            return true;
        } else {
            $this->errors['global'] = 'Erreur lors de l\'enregistrement de l\'oeuvre';
            return false;
        }
    }

    function submit_edit(){
        foreach($this->data as $key => $value){
            $this->oeuvre->$key = $value;
        }

        if($this->oeuvre->save()){
            return true;
        } else {
            $this->errors['global'] = 'Erreur lors de l\'enregistrement de l\'oeuvre';
            return false;
        }
    }

    function submit_delete(){
        if($this->oeuvre->delete()){
            return true;
        } else {
            $this->errors['global'] = 'Erreur lors de la suppression de l\'oeuvre';
            return false;
        }
    }

}