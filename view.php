<?php
    // Dependencies
    require_once __DIR__.'/config/Config.php';
    $oeuvre = new Oeuvre();
    $oeuvre->id = $_GET['id'];
    $oeuvre->hydrate();

    $templates = Templates::instance();
?>

<!-- CONTENU PAGE -->

<?php require $templates->head ?>
<body>
    <?php require $templates->header ?>
    <main>
        <?php
            require $templates->fullView;
        ?>
    </main>
    <?php require $templates->footer ?>
</body>
</html>

<!-- FIN CONTENU PAGE -->