<?php
    // Dependencies
    require_once __DIR__.'/../config/Config.php';

    $templates = Templates::instance();
    $search = [];
    if(!empty($_GET['titre'])) {$search['titre'] = $_GET['titre'];}
    if(!empty($_GET['auteur'])) {$search['auteur'] = $_GET['auteur'];}
    if(!empty($_GET['description'])) {$search['description'] = $_GET['description'];}
    if(!empty($_GET['image'])) {$search['url_image'] = $_GET['image'];}

    $search = array_map(function ($value) {
        $value = trim($value);
        return $value;
    }, $search);

    $formatedSearch = array_map(function ($value) {
        $value = [
            'value' => '%'.$value.'%',
            'operator' => 'LIKE'
        ];
        return $value;
    }, $search);

    if(empty($formatedSearch)) $formatedSearch = [];

    $resultCount = Oeuvre::fetch($formatedSearch, ['select' => 'COUNT(*)']);

    $options = [
        'limit' => $cfg->db_defaultLimit,
        'offset' => $_GET['offset'] ?? 0,
        'order_by' => 'id'
    ];

    $oeuvres = Oeuvre::fetch($formatedSearch, $options);
?>

<?php require $templates->head ?>
<body>
    <?php require $templates->header ?>

<?php
    $action = $_GET['action'] ?? '';
    $text = '';
    if(!empty($action)){
        switch($action){
            case 'update' :
                $text = 'Element <span class="action">mis à jour</span> avec succès.';
                break;
            case 'delete' :
                $text = 'Element <span class="action">supprimé</span> avec succès.';
                break;
            case 'add' :
                $text = 'Element <span class="action">ajouté</span> avec succès.';
                break;
            case 'error' :
                $text = 'Une <span class="action">erreur</span> est survenue :';
                if(!empty($_GET['message'])) $text .= '<br>'.$_GET['message'];
                break;
        }
        
        require $templates->confirmPopup;
        unset($text);
        unset($action);
    }
?>

    <h3>Filter :</h3>
    <?php require $templates->form_search ?>

    <?php
        if(
            !empty($_GET['action'])
            && $_GET['action'] == 'error'
            && !empty($_GET['message'])
        ) {
            echo '<p class="error">'.$_GET['message'].'</p>';
        }
    ?>


    <table class="results">
        <caption>Results :</caption>
        <thead>
            <tr>
                <th>N°</th>
                <th>Id</th>
                <th>Titre</th>
                <th>Auteur</th>
                <th>Description</th>
                <th>Nom image</th>
                <th>Modifier image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <tr><?php require $templates->form_add ?></tr>

            <?php foreach($oeuvres as $key => $oeuvre) : ?>
                <tr><?php require $templates->form_edit ?></tr>
            <?php endforeach ?>
        </tbody>
    </table>
    <div class="pageSelect">
        <?php
            if($resultCount > $cfg->db_defaultLimit){
                $i = 0;
                while($i * $cfg->db_defaultLimit < $resultCount) {
                    $i++;
                    echo '<a href="?offset='.(($i - 1)*$cfg->db_defaultLimit).'">'.$i.'</a>';
                }
            }
        ?>
    </div>
</body>
<?php require $templates->footer ?>

<script defer>
    <?php require $templates->js_admin ?>
</script>