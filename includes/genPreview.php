<?php
    function genPreview($oeuvre){
        if(!isset($oeuvre['title'])) $oeuvre['title'] = "Sans titre";
        if(!isset($oeuvre['artist'])) $oeuvre['artist'] = "Anonyme";
        if(!isset($oeuvre['image'])) throw new Exception("Missing image for oeuvre");
        if(!isset($oeuvre['link'])) throw new Exception("Missing link for oeuvre");

        ob_start();
?>

<article class="oeuvre">
    <a href="<?=$oeuvre['link']?>">
        <img src="<?=$oeuvre['image']?>" alt="<?=$oeuvre['title']?>">
    <h2><?=$oeuvre['title']?></h2>
        <p class="description"><?=$oeuvre['artist']?></p>
    </a>
</article>

<?php
        $html = ob_get_clean();
        return $html;
    }
?>