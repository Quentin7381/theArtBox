<?php
    if(!isset($title)) $title = "Accueil";
?>

<header>
    <a href="<?=$cfg->root?>/index.html"><img src="<?=$cfg->root?>/img/logo.png" alt="Logo Artbox" id="logo"></a>
    <nav>
        <ul>
            <li><a href="<?=$cfg->root?>/index.html"><?=$title?></a></li>
        </ul>
    </nav>
</header>