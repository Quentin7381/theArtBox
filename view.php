<?php
    // Dependencies
    require_once './config/cfg.php';
    require_once './includes/oeuvres.php';
    require_once './includes/genFull.php';
    $oeuvreId = $_GET['id'];
?>

<!-- CONTENU PAGE -->

<?php require './includes/head.php'; ?>
<body>
    <?php require './includes/header.php'; ?>
    <main>
        <?php
            echo genFull($oeuvres[$oeuvreId]);
        ?>
    </main>
    <?php require './includes/footer.php'; ?>
</body>
</html>

<!-- FIN CONTENU PAGE -->