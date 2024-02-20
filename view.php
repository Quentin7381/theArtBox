<?php
    // Dependencies
    require_once __DIR__.'/config/Config.php';
    $oeuvre = new Oeuvre();
    $oeuvre->id = $_GET['id'];
    $oeuvre->hydrate();

    $templates = Templates::instance();
?>

<!-- CONTENU PAGE -->

<?php require_once $templates->head ?>
<body>
    <?php require_once $templates->header ?>
    <main>
        <?php
            $template = $templates->fullView;
            if(!$oeuvre->hydrated) {
                $template= $templates->notFound;
            }
            require_once $template;
        ?>
    </main>
    <?php require_once $templates->footer ?>
</body>
</html>

<!-- FIN CONTENU PAGE -->