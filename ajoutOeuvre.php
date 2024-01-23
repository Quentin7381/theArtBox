<?php
    // Dependencies
    require_once __DIR__.'/config/cfg.php';
?>

<!-- CONTENU PAGE -->

<?php require __DIR__.'/includes/head.php'; ?>
<?php require __DIR__.'/includes/header.php'; ?>

<form class="ajoutOeuvre">
    <label for="title">Titre de l'oeuvre</label>
    <input type="text" name="title" id="title" placeholder="Titre de l'oeuvre">
    <label for="artist">Artiste</label>
    <input type="text" name="artist" id="artist" placeholder="Nom de l'artiste">
    <label for="image">Image</label>
    <input type="file" name="image" id="image" placeholder="Image de l'oeuvre">
    <label for="link">Lien</label>
    <input type="text" name="link" id="link" placeholder="Lien de l'oeuvre">
    <label for="description">Description</label>
    <textarea name="description" id="description" placeholder="Description de l'oeuvre"></textarea>
    <input type="submit" value="Ajouter l'oeuvre">
</form>

<?php require __DIR__.'/includes/footer.php'; ?>