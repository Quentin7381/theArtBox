<?php
    function genFull($oeuvre){
        if(!isset($oeuvre['title'])) $oeuvre['title'] = "Sans titre";
        if(!isset($oeuvre['artist'])) $oeuvre['artist'] = "Anonyme";
        if(!isset($oeuvre['image'])) throw new Exception("Missing image for oeuvre");
        if(!isset($oeuvre['link'])) throw new Exception("Missing link for oeuvre");
        if(!isset($oeuvre['description'])) $oeuvre['description'] = "";

        ob_start();
?>

<article id="detail-oeuvre">
    <div id="img-oeuvre">
        <img src="<?=$oeuvre['image']?>" alt="<?=$oeuvre['title']?>">
    </div>
    <div id="contenu-oeuvre">
        <h1><?=$oeuvre['title']?></h1>
        <p class="description"><?=$oeuvre['artist']?></p>
        <p class="description-complete"><?=$oeuvre['description']?></p>
    </div>
</article>

<?php
        $html = ob_get_clean();
        return $html;
    }