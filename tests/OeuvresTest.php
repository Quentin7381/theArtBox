<?php

require_once __DIR__.'/TestSetup.php';

class OeuvresTest extends TestSetup{
    static protected $className = 'Oeuvre';
    protected $BDD = null;
    protected $Oeuvre = null;
    protected $lastSql = null;
    protected $stmtMock = null;

    function setUp():void{
        parent::setUp();

        // Setup the BDD mock in the Oeuvre class
        $this->stmtMock = $this->createMock(PDOStatement::class);
        $this->BDD->method('prepare')->willReturn($this->stmtMock);
        $this->stmtMock->method('execute')->willReturn(true);
        $this->stmtMock->method('fetchAll')->willReturn([]);
        $this->Oeuvre['properties']['bdd']->setValue(null, $this->BDD);

        // Listen to the last SQL query
        $this->BDD->expects($this->any())
            ->method('prepare')
            ->with($this->callback(function($sql){
                $this->lastSql = $sql;
                return true;
            }));
    }

    function test__construct(){
        // ----- POSITIONAL ARGUMENTS -----
        // Full constructor
        $oeuvre = new Oeuvre('titre', 'artiste', 'url_image', 'description');
        
        // Properties are filled
        $this->assertEquals('titre', $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals('artiste', $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals('url_image', $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals('description', $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);

        // Partial constructor
        $oeuvre = new Oeuvre('titre', 'artiste');

        // Properties are filled and others are null
        $this->assertEquals('titre', $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals('artiste', $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);

        // No arguments
        $oeuvre = new Oeuvre();

        // Properties are null
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);

        // ----- ASSOCIATIVE ARRAY -----
        // Full constructor
        $oeuvre = new Oeuvre([
            'titre' => 'titre',
            'artiste' => 'artiste',
            'url_image' => 'url_image',
            'description' => 'description'
        ]);

        // Properties are filled
        $this->assertEquals('titre', $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals('artiste', $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals('url_image', $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals('description', $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);

        // Partial constructor
        $oeuvre = new Oeuvre([
            'titre' => 'titre',
            'artiste' => 'artiste'
        ]);

        // Properties are filled and others are null
        $this->assertEquals('titre', $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals('artiste', $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);

        // No arguments
        $oeuvre = new Oeuvre([]);

        // Properties are null
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);

        // ----- OBJECT -----
        // Full constructor
        $oeuvre = new StdClass();
        $oeuvre->titre = 'titre';
        $oeuvre->artiste = 'artiste';
        $oeuvre->url_image = 'url_image';
        $oeuvre->description = 'description';

        $oeuvre = new Oeuvre($oeuvre);

        // Properties are filled
        $this->assertEquals('titre', $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals('artiste', $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals('url_image', $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals('description', $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);

        // Partial constructor
        $oeuvre = new StdClass();
        $oeuvre->titre = 'titre';
        $oeuvre->artiste = 'artiste';

        $oeuvre = new Oeuvre($oeuvre);

        // Properties are filled and others are null
        $this->assertEquals('titre', $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals('artiste', $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);

        // No arguments
        $oeuvre = new StdClass();

        $oeuvre = new Oeuvre($oeuvre);

        // Properties are null
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals(null, $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);        

        // ----- INVALID ARGUMENTS -----
        // Too many arguments
        $this->expectExceptionMessage('In Oeuvre::__construct(): Invalid arguments. Arguments can be an associative array, an object, or positional arguments (titre, artiste, url_image, description, id)');

        $oeuvre = new Oeuvre('titre', 'artiste', 'url_image', 'description', 'id', 'too much');
    }

    function test__fromArray(){
        $oeuvre = Oeuvre::from_array([
            'titre' => 'titre',
            'artiste' => 'artiste',
            'url_image' => 'url_image',
            'description' => 'description'
        ]);
        
        // Result object is the expected one
        $this->assertEquals('titre', $this->Oeuvre['properties']['values']->getValue($oeuvre)['titre']);
        $this->assertEquals('artiste', $this->Oeuvre['properties']['values']->getValue($oeuvre)['artiste']);
        $this->assertEquals('url_image', $this->Oeuvre['properties']['values']->getValue($oeuvre)['url_image']);
        $this->assertEquals('description', $this->Oeuvre['properties']['values']->getValue($oeuvre)['description']);
    }

    function test__fetch(){
        
        // No arguments
        Oeuvre::fetch();
        $this->assertEquals(
            'SELECT * '.
            'FROM oeuvres '.
            'WHERE 1',
            $this->lastSql
        );

        // Filters
        Oeuvre::fetch([
            'titre' => 'titre',
            'artiste' => 'artiste',
            'url_image' => 'url_image',
            'description' => 'description'
        ]);
        $this->assertEquals(
            'SELECT * '.
            'FROM oeuvres '.
            'WHERE 1 '.
                'AND titre = (:param_titre_0) '.
                'AND artiste = (:param_artiste_0) '.
                'AND url_image = (:param_url_image_0) '.
                'AND description = (:param_description_0)', 
            $this->lastSql
        );

        // Filters with operators
        Oeuvre::fetch([
            'titre' => [
                'value' => 'titre',
                'operator' => '!='
            ],
            'artiste' => [
                'value' => 'artiste',
                'operator' => 'LIKE'
            ],
            'url_image' => [
                'value' => 'url_image',
                'operator' => 'NOT LIKE'
            ],
            'description' => [
                'value' => 'description',
                'operator' => '='
            ],
            'id' => [
                'value' => 1,
                'operator' => '>'
            ]
        ]);
        $this->assertEquals(
            'SELECT * '.
            'FROM oeuvres '.
            'WHERE 1 '.
                'AND titre != (:param_titre_0) '.
                'AND artiste LIKE (:param_artiste_0) '.
                'AND url_image NOT LIKE (:param_url_image_0) '.
                'AND description = (:param_description_0) '.
                'AND id > (:param_id_0)', 
            $this->lastSql
        );

        // Filters with multiple values
        Oeuvre::fetch([
            'titre' => ['titre_1', 'titre_2', 'titre_3']
        ]);
        $this->assertEquals(
            'SELECT * '.
            'FROM oeuvres '.
            'WHERE 1 '.
                'AND titre IN (:param_titre_0, :param_titre_1, :param_titre_2)', 
            $this->lastSql
        );
        
        // ----- OPTIONS -----
        // Order by
        Oeuvre::fetch([], [
            'order_by' => 'titre'
        ]);

        $this->assertEquals(
            'SELECT * '.
            'FROM oeuvres '.
            'WHERE 1 '.
            'ORDER BY titre', 
            $this->lastSql
        );

        // Order by & order
        Oeuvre::fetch([], [
            'order_by' => 'titre',
            'order' => 'DESC'
        ]);

        $this->assertEquals(
            'SELECT * '.
            'FROM oeuvres '.
            'WHERE 1 '.
            'ORDER BY titre DESC', 
            $this->lastSql
        );

        // Limit
        Oeuvre::fetch([], [
            'limit' => 10
        ]);

        $this->assertEquals(
            'SELECT * '.
            'FROM oeuvres '.
            'WHERE 1 '.
            'LIMIT 10', 
            $this->lastSql
        );

        // Offset
        Oeuvre::fetch([], [
            'offset' => 10
        ]);

        $this->assertEquals(
            'SELECT * '.
            'FROM oeuvres '.
            'WHERE 1 '.
            'OFFSET 10', 
            $this->lastSql
        );

        // -- Select --
        // count
        $this->stmtMock
            ->method('fetchAll')
            ->willReturn([
                ['COUNT(*)' => 10]
        ]);
        $result = Oeuvre::fetch([], [
            'select' => 'COUNT'
        ]);
        $this->assertEquals(
            'SELECT COUNT(*) '.
            'FROM oeuvres '.
            'WHERE 1', 
            $this->lastSql
        );
        $this->assertEquals(10, $result);

        // column name
        Oeuvre::fetch([], [
            'select' => 'titre'
        ]);
        $this->assertEquals(
            'SELECT titre '.
            'FROM oeuvres '.
            'WHERE 1', 
            $this->lastSql
        );

        // multiple columns
        Oeuvre::fetch([], [
            'select' => ['titre', 'artiste']
        ]);

        $this->assertEquals(
            'SELECT titre, artiste '.
            'FROM oeuvres '.
            'WHERE 1', 
            $this->lastSql
        );

        // invalid column name
        $this->expectExceptionMessage('In Oeuvre::fetch(): Invalid argument. $options[\'select\']. String value must match a column names (titre, artiste, url_image, description, id) or implemented functions (COUNT)');
        Oeuvre::fetch([], [
            'select' => 'invalid'
        ]);

        $this->expectExceptionMessage('In Oeuvre::fetch(): Invalid argument. $options[\'select\']. Array values must match column names (titre, artiste, url_image, description, id)');
        Oeuvre::fetch([], [
            'select' => ['titre', 'invalid']
        ]);
    }

    function test__hydrate(){
        $oeuvre = new Oeuvre(['id' => 99]);
        $oeuvre->hydrate();

        $this->stmtMock->expects($this->any())
            ->method('fetch')
            ->willReturn([
                'titre' => 'titre',
                'artiste' => 'artiste',
                'url_image' => 'url_image',
                'description' => 'description'
            ]);

        $this->assertEquals(
            'SELECT * '.
            'FROM oeuvres '.
            'WHERE id = :id',
            $this->lastSql
        );
    }

    function test__save(){
        $this->BDD->method('lastInsertId')->willReturn(22);

        $oeuvre = new Oeuvre([
            'titre' => 'titre',
            'artiste' => 'artiste',
            'url_image' => 'url_image',
            'description' => 'description'
        ]);

        // Insert
        $oeuvre->save();

        $this->assertEquals(
            'INSERT INTO oeuvres (titre, artiste, url_image, description) '.
            'VALUES (:titre, :artiste, :url_image, :description)',
            $this->lastSql
        );

        $this->assertEquals(22, $oeuvre->id);

        // Update
        $oeuvre->id = 99;
        $oeuvre->save();

        $this->assertEquals(
            'UPDATE oeuvres '.
            'SET titre = :titre, artiste = :artiste, url_image = :url_image, description = :description '.
            'WHERE id = :id',
            $this->lastSql
        );
    }

    function test__to_array(){
        $oeuvre = new Oeuvre();
        $oeuvre->titre = 'titre_7';
        $oeuvre->artiste = 'artiste_7';
        $oeuvre->url_image = 'image_7';
        $oeuvre->description = 'description_7';

        $this->assertEquals([
            'id' => null,
            'titre' => 'titre_7',
            'artiste' => 'artiste_7',
            'url_image' => 'image_7',
            'description' => 'description_7'
        ], $oeuvre->to_array());
    }

    function test__to_array_multiple(){
        $oeuvres = [
            Oeuvre::from_array([
                'titre' => 'titre_8',
                'artiste' => 'artiste_8',
                'url_image' => 'image_8',
                'description' => 'description_8'
            ]),
            Oeuvre::from_array([
                'titre' => 'titre_9',
                'artiste' => 'artiste_9',
                'url_image' => 'image_9',
                'description' => 'description_9'
            ]),
            Oeuvre::from_array([
                'titre' => 'titre_10',
                'artiste' => 'artiste_10',
                'url_image' => 'image_10',
                'description' => 'description_10'
            ])
        ];

        $this->assertEquals([
            [
                'id' => null,
                'titre' => 'titre_8',
                'artiste' => 'artiste_8',
                'url_image' => 'image_8',
                'description' => 'description_8'
            ],
            [
                'id' => null,
                'titre' => 'titre_9',
                'artiste' => 'artiste_9',
                'url_image' => 'image_9',
                'description' => 'description_9'
            ],
            [
                'id' => null,
                'titre' => 'titre_10',
                'artiste' => 'artiste_10',
                'url_image' => 'image_10',
                'description' => 'description_10'
            ]
        ], Oeuvre::to_array_multiple($oeuvres));

        // Throw an exception if one of the objects is not an Oeuvre
        $oeuvres[] = 'not an Oeuvre';
        $this->expectExceptionMessage('In Oeuvre::to_array_multiple(): $instances must be an array of Oeuvre instances');

        Oeuvre::to_array_multiple($oeuvres);
    }
}