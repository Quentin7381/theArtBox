<?php

require_once '../config/Config.php';

// get the value of submit button
// var_dump($_POST);
// var_dump($_FILES);

function saveImage($image){
    global $cfg;

    $image_name = $image['name'];
    $base_name = pathinfo($image_name, PATHINFO_FILENAME);
    $image_tmp_name = $image['tmp_name'];
    //We cut the basename to 75 characters
    if(strlen($base_name) > 75){
        $base_name = substr($base_name, 0, 75);
    }
    $image_size = $image['size'];
    $image_error = $image['error'];
    $image_type = $image['type'];

    // get true extension of image, even if it have been renamed
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $image_extension = finfo_file($finfo, $image_tmp_name);
    $image_extension= str_replace('image/', '', $image_extension);
    finfo_close($finfo);

    if($image_error === 0){
        if($image_size <= 1000000){
            $n = 2;
            while(file_exists($cfg->path_img.$image_name)){
                $image_name = $base_name.'_'.$n.'.'.$image_extension;
            }

            $image_destination = $cfg->path_img.$image_name;
            move_uploaded_file($image_tmp_name, $image_destination);
            return $image_name;
        }
    }
}

function getErrors($data){
    if(empty($_POST['titre'])) return 'Le titre est obligatoire';
    if(empty($_POST['artiste'])) return 'L\'artiste est obligatoire';
    if(empty($_POST['description'])) return 'La description est obligatoire';
    if(strlen($data['description']) <= 3) return 'La description doit faire plus de 3 caractères';
    if(strlen($_POST['titre']) > 160) return 'Le titre doit faire moins de 160 caractères';
    if(strlen($_POST['artiste']) > 80) return 'L\'artiste doit faire moins de 80 caractères';
    if(empty($_FILES)) return 'L\'image est obligatoire';
    return null;
}

$error = null;
if(!in_array($_POST['action'], ['search, delete'])) $error = getErrors($_POST);
if(!empty($error)){
    header('Location: '.$cfg->url_admin.'/?action=error&message='.$error);
    exit;
}

// Do the changes
switch($_POST['action']){
    case 'search' :
        header('Location: '.$cfg->url_admin.'/index.php?titre='.$_POST['titre'].'&artiste='.$_POST['artiste'].'&description='.$_POST['description'].'&image='.$_POST['image_name']);
        exit;
    case 'update' :
        $oeuvre = Oeuvre::fetch(['id' => $_POST['id']]);
        $oeuvre = $oeuvre[0];
        $oeuvre->titre = $_POST['titre'];
        $oeuvre->artiste = $_POST['artiste'];
        $oeuvre->description = $_POST['description'];
        $oeuvre->url_image = saveImage($_FILES['image']);
        $oeuvre->save();
        header('Location: '.$cfg->url_admin.'/?action=update');
        exit;
    case 'add' :
        $oeuvre = new Oeuvre();
        $oeuvre->titre = $_POST['titre'];
        $oeuvre->artiste = $_POST['artiste'];
        $oeuvre->description = $_POST['description'];
        $oeuvre->url_image = saveImage($_FILES['image']);
        $oeuvre->save();
        header('Location: '.$cfg->url_admin.'/?action=add');
        exit;
    case 'delete' :
        $oeuvre = Oeuvre::fetch(['id' => $_POST['id']]);
        $oeuvre = $oeuvre[0];
        $oeuvre->delete();
        header('Location: '.$cfg->url_admin.'/?action=delete');
        exit;
}