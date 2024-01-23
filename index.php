<?php
    // Dependencies
    require_once __DIR__.'/config/cfg.php';
    require_once __DIR__.'/includes/oeuvres.php';
    require_once __DIR__.'/includes/genPreview.php';
?>

<!-- CONTENU PAGE -->

<?php require __DIR__.'/includes/head.php'; ?>
<body>
    <?php require __DIR__.'/includes/header.php'; ?>
    <main>
        <div id="liste-oeuvres">
            <?php
                foreach($oeuvres as $key => $oeuvre) :
                    echo genPreview($oeuvre);
                endforeach;
            ?>
        </div>
    </main>
    <?php require __DIR__.'/includes/footer.php'; ?>
</body>
</html>

<!-- FIN CONTENU PAGE -->