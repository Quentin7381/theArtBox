<?php
    // Dependencies
    require_once __DIR__.'/config/Config.php';
    $templates = Templates::instance();
    $oeuvres = Oeuvre::fetch();
    foreach($oeuvres as $oeuvre){
        $oeuvre->hydrate();
    }
    
?>

<!-- CONTENU PAGE -->

<?php require $templates->head ?>
<body>
    <?php require $templates->header ?>
    <main>
        <div id="liste-oeuvres">
            <?php
                foreach($oeuvres as $oeuvre) :
                    require $templates->preview;
                endforeach;
            ?>
        </div>
    </main>
    <?php require $templates->footer ?>
</body>
</html>

<!-- FIN CONTENU PAGE -->