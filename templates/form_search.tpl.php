<form class="form_search" action="submit.php" method="POST" enctype="multipart/form-data">
    <div class="inputCouple">
        <label for="titre">Titre</label>
        <input type="text" name="titre" placeholder="Titre de l'oeuvre" value="<?= $search['titre'] ?? '' ?>">
    </div>
    <div class="inputCouple">
        <label for="artiste">Auteur</label>
        <input type="text" name="artiste" placeholder="Auteur de l'oeuvre" value="<?= $search['artiste'] ?? '' ?>">
    </div>
    <div class="inputCouple input_description">
        <label for="description">Description</label>
        <textarea name="description" id="" cols="16" rows="2"><?= $search['description'] ?? '' ?></textarea>
    </div>
    <div class="inputCouple">
        <label for="image_name">Nom de l'image</label>
        <input type="text" name="image_name" placeholder="Nom de l'image" value="<?= $search['image_name'] ?? '' ?>">
    </div>
    <input type="submit" name="action" value="search">
</form>