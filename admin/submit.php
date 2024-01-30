<?php

require_once '../config/Config.php';

// get the value of submit button
// var_dump($_POST);
// var_dump($_FILES);

function saveImage($image){
    global $cfg;

    $image_name = $image['name'];
    $image_tmp_name = $image['tmp_name'];
    $image_size = $image['size'];
    $image_error = $image['error'];
    $image_type = $image['type'];

    $image_extension = explode('.', $image_name);
    $image_extension = strtolower(end($image_extension));


    if($image_error === 0){
        if($image_size <= 1000000){
            $n = 2;
            $base_name = pathinfo($image_name, PATHINFO_FILENAME);
            while(file_exists($cfg->path_root.'/img/'.$image_name)){
                $image_name = $base_name.'_'.$n.'.'.$image_extension;
            }

            $image_destination = $cfg->path_root.'/img/'.$image_name;
            move_uploaded_file($image_tmp_name, $image_destination);
            return $image_name;
        }
    }

}

switch($_POST['action']){
    case 'search' :
        header('Location: '.$cfg->url_root.'/admin/index.php?titre='.$_POST['titre'].'&artiste='.$_POST['artiste'].'&description='.$_POST['description'].'&image='.$_POST['image_name']);
        exit;
    case 'update' :
        $oeuvre = Oeuvre::fetch(['id' => $_POST['id']]);
        $oeuvre = $oeuvre[0];
        $oeuvre->titre = $_POST['titre'];
        $oeuvre->artiste = $_POST['artiste'];
        $oeuvre->description = $_POST['description'];
        $oeuvre->url_image = saveImage($_FILES['image']);
        $oeuvre->save();
        header('Location: '.$cfg->url_root.'admin/?action=update');
        exit;
    case 'add' :
        $oeuvre = new Oeuvre();
        $oeuvre->titre = $_POST['titre'];
        $oeuvre->artiste = $_POST['artiste'];
        $oeuvre->description = $_POST['description'];
        $oeuvre->url_image = saveImage($_FILES['image']);
        $oeuvre->save();
        header('Location: '.$cfg->url_root.'admin/?action=add');
        exit;
    case 'delete' :
        $oeuvre = Oeuvre::fetch(['id' => $_POST['id']]);
        $oeuvre = $oeuvre[0];
        $oeuvre->delete();
        header('Location: '.$cfg->url_root.'admin/?action=delete');
        exit;
}