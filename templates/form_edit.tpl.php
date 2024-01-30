
    <form action="submit" method="POST" enctype="multipart/form-data">
        <td>
            <?= $key + 1 + ($_GET['offset'] ?? 0) ?>
        </td>
        <td>
            <strong>
                <?= $oeuvre->id ?>
            </strong>
            <input type="hidden" name="id" value="<?= $oeuvre->id ?>">
        </td>
        <td>
            <input type="text" name="titre" placeholder="Titre de l'oeuvre" value="<?= $oeuvre->titre ?>" required>
        </td>
        <td>
            <input type="text" name="artiste" placeholder="Auteur de l'oeuvre" value="<?= $oeuvre->artiste ?>" required>
        </td>
        <td>
            <textarea name="description" id="" cols="16" rows="2" required><?= $oeuvre->description ?></textarea>
        </td>
        <td>
            <?= $oeuvre->url_image ?>
            <input type="hidden" name="image_name" value="<?= $oeuvre->url_image ?>">
        </td>
        <td>
            <label class="fileBtn" for="edit_image<?= isset($key) ? '_'.$key : ''?>">
                <img src="<?= $cfg->url_root.'img/'.$oeuvre->url_image ?>" alt="edit image">
            </label>
            <input id="edit_image<?= isset($key) ? '_'.$key : ''?>" type="file" name="image">
        </td>
        <td>
            <input type="submit" name="action" value="update">
            <input type="submit" name="action" value="delete">
        </td>
    </form>