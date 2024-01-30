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
            <input type="text" name="titre" placeholder="Titre de l'oeuvre" value="<?= $oeuvre->titre ?>" required data-key="<?= $key ?>">
        </td>
        <td>
            <input type="text" name="artiste" placeholder="Auteur de l'oeuvre" value="<?= $oeuvre->artiste ?>" required data-key="<?= $key ?>">
        </td>
        <td>
            <textarea name="description" id="" cols="16" rows="2" required data-key="<?= $key ?>"><?= $oeuvre->description ?></textarea>
        </td>
        <td>
            <span class="image_name" id="image_name_<?=$key?>" data-key="<?= $key ?>"><?= $oeuvre->url_image ?></span>
            <input type="hidden" name="image_name" value="<?= $oeuvre->url_image ?>" data-key="<?= $key ?>">
        </td>
        <td>
            <label class="fileBtn" for="edit_image<?= isset($key) ? '_'.$key : ''?>" data-key="<?= $key ?>">
                <img src="<?= $cfg->url_img.$oeuvre->url_image ?>" alt="🢙" data-key="<?= $key ?>">
            </label>
            <input id="edit_image<?= isset($key) ? '_'.$key : ''?>" type="file" name="image" data-key="<?= $key ?>">
        </td>
        <td>
            <input type="submit" name="action" value="update" data-key="<?= $key ?>">
            <input type="submit" name="action" value="delete" data-key="<?= $key ?>">
        </td>
    </form>