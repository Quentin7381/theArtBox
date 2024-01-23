<?php

require_once __DIR__.'/../config/Config.php';
require_once __DIR__.'/../includes/Autoload.php';

$cfg->override('db_name', 'theartbox_test');

class TestSetup extends PHPUnit\Framework\TestCase{

    public function setUp():void{
        $db = BDD::getInstance();
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // reset the table entries
        $db->exec('DELETE FROM oeuvres');

        // re-create the entries
        $stmt = $db->prepare('INSERT INTO oeuvres (titre, artiste, url_image, description) VALUES (:titre, :artiste, :image, :description)');
        $stmt->execute([
            'titre' => 'titre_1',
            'artiste' => 'artiste_1',
            'image' => 'image_1',
            'description' => 'description_1'
        ]);
        $stmt->execute([
            'titre' => 'titre_2',
            'artiste' => 'artiste_2',
            'image' => 'image_2',
            'description' => 'description_2'
        ]);
        $stmt->execute([
            'titre' => 'titre_3',
            'artiste' => 'artiste_1',
            'image' => 'image_3',
            'description' => 'description_3'
        ]);
    }

    function test__phpunitWorks(){
        $this->assertTrue(true);
    }

    // function test__transactionWorks(){
    //     $db = BDD::getInstance();
        
    //     $db->exec('SAVEPOINT test_transaction');
    //     $stmt = $db->prepare('INSERT INTO oeuvres (titre, artiste, url_image, description) VALUES (:titre, :artiste, :image, :description)');
    //     $stmt->execute([
    //         'titre' => 'Test',
    //         'artiste' => 'Test',
    //         'image' => 'Test',
    //         'description' => 'Test'
    //     ]);
    //     $stmt = $db->prepare('SELECT * FROM oeuvres WHERE titre = :titre');
    //     $stmt->execute(['titre' => 'Test']);
    //     $this->assertTrue(true);

    //     $this->tearDown();

    //     $stmt = $db->prepare('SELECT * FROM oeuvres WHERE titre = :titre');
    //     $stmt->execute(['titre' => 'Test']);
    //     // $this->assertEquals(0, $stmt->rowCount());
    // }

}