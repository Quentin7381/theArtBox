<?php
    if(!isset($title)) $title = "Accueil";
?>

<header>
    <a href="<?=$cfg->url_root?>"><img src="<?=$cfg->url_img?>logo.png" alt="Logo Artbox" id="logo"></a>
    <nav>
        <ul>
            <li><a href="<?=$cfg->url_root?>"><?=$title?></a></li>
        </ul>
    </nav>
</header>