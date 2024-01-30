<form class="form_add" action="submit.php" method="POST" enctype="multipart/form-data">
    <td></td>
    <td></td>
    <td>
        <input type="text" name="titre" placeholder="Titre de l'oeuvre" required data-key="add">
    </td>
    <td>
        <input type="text" name="artiste" placeholder="Auteur de l'oeuvre" required data-key="add">
    </td>
    <td>
        <textarea name="description" id="" cols="16" rows="1" required data-key="add"></textarea>
    </td>
    <td>
        <span class="image_name" id="image_name_edit" data-key="add">
            
        </span>
    </td>
    <td>
        <label class="fileBtn shadow" for="add_image" data-key="add">
            <img src="" alt="🢙" data-key="add">            
        </label>
        <input id="add_image" type="file" name="image" required data-key="add">
    </td>

    <td><input type="submit" name="action" value="add"></td>
</form>