<?php

    // Dependencies
    require_once './config/cfg.php';
    require_once './includes/oeuvres.php';
    require_once './includes/genPreview.php';
?>

<!-- CONTENU PAGE -->

<?php require './includes/head.php'; ?>
<body>
    <?php require './includes/header.php'; ?>
    <main>
        <div id="liste-oeuvres">
            <?php
                foreach($oeuvres as $key => $oeuvre){
                    echo genPreview($oeuvre);
                }
            ?>
        </div>
    </main>
    <?php require './includes/footer.php'; ?>
</body>
</html>

<!-- FIN CONTENU PAGE -->