<?php
    if(!isset($title)) $title = "Accueil";
?>

<header>
    <a href="<?=$cfg->urlRoot?>"><img src="<?=$cfg->urlRoot?>/img/logo.png" alt="Logo Artbox" id="logo"></a>
    <nav>
        <ul>
            <li><a href="<?=$cfg->urlRoot?>"><?=$title?></a></li>
        </ul>
    </nav>
</header>