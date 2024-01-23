<?php
    // Dependencies
    require_once __DIR__.'/config/cfg.php';
    require_once __DIR__.'/includes/oeuvres.php';
    require_once __DIR__.'/includes/genFull.php';
    $oeuvreId = $_GET['id'];
?>

<!-- CONTENU PAGE -->

<?php require __DIR__.'/includes/head.php'; ?>
<body>
    <?php require __DIR__.'/includes/header.php'; ?>
    <main>
        <?php
            echo genFull($oeuvres[$oeuvreId]);
        ?>
    </main>
    <?php require __DIR__.'/includes/footer.php'; ?>
</body>
</html>

<!-- FIN CONTENU PAGE -->