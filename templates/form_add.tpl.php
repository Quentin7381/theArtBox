<form class="form_add" action="submit.php" method="POST" enctype="multipart/form-data">
    <td></td>
    <td></td>
    <td>
        <input type="text" name="titre" placeholder="Titre de l'oeuvre" required>
    </td>
    <td>
        <input type="text" name="artiste" placeholder="Auteur de l'oeuvre" required>
    </td>
    <td>
        <textarea name="description" id="" cols="16" rows="1" required></textarea>
    </td>
    <td></td>
    <td>
        <label class="fileBtn" for="add_image">+</label>
        <input id="add_image" type="file" name="image" required>
    </td>

    <td><input type="submit" name="action" value="add"></td>
</form>