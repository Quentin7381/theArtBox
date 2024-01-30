<tr>
    <td><a href="admin?mID="<?= $oeuvre->id ?>><?= $oeuvre->titre ?></a></td>
    <td><?= $oeuvre->artiste ?></td>
    <td><?= $oeuvre->date ?></td>
    <td><?= mb_strimwidth($oeuvre->description, 0, 50, '...') ?></td>
    <td><?= $oeuvre->image ?>
</tr>