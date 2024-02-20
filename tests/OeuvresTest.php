<?php

require_once __DIR__.'/TestSetup.php';

use ExceptionFactory as EF;

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
        // $this->stmtMock->method('fetchAll')->willReturn([0=>'value']);
        $this->Oeuvre['properties']['bdd']->setValue(null, $this->BDD);

        // Listen to the last SQL query
        $this->BDD->expects($this->any())
            ->method('prepare')
            ->with($this->callback(function($sql){
                $this->lastSql = $sql;
                return true;
            }));
    }

    function tearDown():void{
        $this->Oeuvre['properties']['columns']->setValue(null, [
            'id',
            'titre',
            'artiste',
            'url_image',
            'description'
        ]);
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
        $this->expectExceptionCode(EF::ARGUMENT_WRONG_TYPE);

        new Oeuvre('titre', 'artiste', 'url_image', 'description', 'id', 'too much');
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

    function test__get(){
        $mock = $this->getMockBuilder(Oeuvre::class)
            ->setMethods(['get_column', 'get_link', 'get_image'])
            ->getMock();

        $columns = ['<col_1>', '<col_2>', '<col_3>'];
        $values = ['<val_1>', '<val_2>', '<val_3>'];

        $this->Oeuvre['properties']['columns']->setValue($mock, $columns);
        $this->Oeuvre['properties']['values']->setValue($mock, $values);

        // Si une fonction get_<key> existe, elle est appelée
        $keys = ['link', 'image'];
        foreach($keys as $key){
            $mock->expects($this->once())
                ->method('get_'.$key)
                ->willReturn('value');

            $this->assertEquals('value', $mock->$key);
        }

        // Si la clé existe dans les colonnes, get_column est appelée avec la clé en argument

        $mock->expects($this->exactly(count($columns)))
            ->method('get_column')
            ->withConsecutive(
                [$columns[0]],
                [$columns[1]],
                [$columns[2]]
            )
            ->willReturn('value');

        foreach($columns as $key){
            $this->assertEquals('value', $mock->$key);
        }

        // Si aucune fonction get_<key> n'existe et que la clé n'est pas dans les colonnes
        // la valeur est cherchée tel quel
        $this->assertEquals($values, $mock->values);
    }

    function test__get_column(){
        $mock = $this->getMockBuilder(Oeuvre::class)
            ->setMethods(['hydrate'])
            ->getMock();

        // Collumn vas chercher la valeur dans le tableau associatif $values
        $values = [
            'titre' => '<titre>',
            'artiste' => '<artiste>',
            'url_image' => '<url_image>',
            'description' => '<description>'
        ];

        $this->Oeuvre['properties']['values']->setValue($mock, $values);

        foreach($values as $key => $value){
            $actual = $mock->get_column($key);
            $this->assertEquals($value, $actual);
        }

        // Si la clé est à null, get_column tente d'hydrater l'objet
        $mock->expects($this->once())
            ->method('hydrate');
        
        $values['titre'] = null;
        $this->Oeuvre['properties']['values']->setValue($mock, $values);

        $mock->get_column('titre');

        // Si l'objet est déjà hydraté, la méthode n'est pas appelée
        $this->Oeuvre['properties']['hydrated']->setValue($mock, true);

        $mock->get_column('titre'); // on s'appuie sur le once() précédent
    }

    function test__get_link(){
        $cfg = Config::getInstance();
        $cfg->override('url_root', '<url_root>/');

        // get_link utilise l'id et la configuration pour générer un lien
        $oeuvre = new Oeuvre(['id' => 99]);

        $expected = '<url_root>/view/?id=99';
        $actual = $oeuvre->get_link();
        $this->assertEquals($expected, $actual);

        // Si l'id est null, get_link retourne null
        $oeuvre = new Oeuvre(['id' => null]);

        $this->assertEquals(null, $oeuvre->get_link());
    }

    function test__get_image(){
        $cfg = Config::getInstance();
        $cfg->override('url_img', '<url_img>/');

        // get_image utilise l'url_image et la configuration pour générer un lien
        $oeuvre = new Oeuvre();
        $this->Oeuvre['properties']['values']->setValue($oeuvre, ['url_image' => 'image.jpg']);

        $expected = '<url_img>/image.jpg';
        $actual = $oeuvre->get_image();
        $this->assertEquals($expected, $actual);

        // Si l'url_image est null, get_image tente d'hydrater l'objet
        $mock = $this->getMockBuilder(Oeuvre::class)
            ->setMethods(['hydrate'])
            ->getMock();
        $mock->expects($this->once())
            ->method('hydrate');

        $this->Oeuvre['properties']['values']->setValue($mock, ['url_image' => null]);
        $this->Oeuvre['properties']['hydrated']->setValue($mock, false);

        $mock->get_image();

        // Si l'url_image est null malgré tout, get_image retourne null
        $oeuvre = new Oeuvre(['url_image' => null]);

        $this->assertEquals(null, $oeuvre->get_image());
    }

    function test__set(){
        $mock = $this->getMockBuilder(Oeuvre::class)
            ->setMethods(['set_column'])
            ->getMock();

        $columns = ['<col_1>', '<col_2>', '<col_3>'];
        $values = ['<val_1>', '<val_2>', '<val_3>'];

        $this->Oeuvre['properties']['columns']->setValue($mock, $columns);
        $this->Oeuvre['properties']['values']->setValue($mock, $values);

        // Cette partie du test a ete desactivee car aucune fonction valide n'implemente set_<key>

        // // Si une fonction set_<key> existe, elle est appelée
        // // Note: l'utilisation de column est erronee ici, mais seule la méthode set_column est implémentée
        // $mock->expects($this->once())
        //     ->method('set_column')
        //     ->with('value');

        // try{
        //     $mock->column = 'value';
        // } catch(Throwable $e){
        //     // On ignore l'exception due à l'appel incorrect de la méthode
        // }

        // Si la clé existe dans les colonnes, set_column est appelée avec la clé et la valeur en argument
        $mock = $this->getMockBuilder(Oeuvre::class)
            ->disableOriginalConstructor()
            ->setMethods(['set_column'])
            ->getMock();

        $this->Oeuvre['properties']['columns']->setValue($mock, $columns);
        $this->Oeuvre['properties']['values']->setValue($mock, $values);

        $mock->expects($this->exactly(count($columns)))
            ->method('set_column')
            ->withConsecutive(
                [$columns[0], 'value_1'],
                [$columns[1], 'value_2'],
                [$columns[2], 'value_3']
            );

        foreach($columns as $i => $key){
            $mock->$key = 'value_'.($i+1);
        }

        // Si aucune fonction set_<key> n'existe et que la clé n'est pas dans les colonnes
        // une user notice est générée
        $this->expectException('PHPUnit\Framework\Error\Notice');
        $oeuvre = new Oeuvre();
        $oeuvre->invalid = 'value';
    }

    function test__set_column(){
        $mock = $this->getMockBuilder(Oeuvre::class)
            ->setMethods(['htmlspecialchars', 'trim'])
            ->getMock();
        
        // set_column vas chercher la valeur dans le tableau associatif $values
        $values = [
            'titre' => '<titre>',
            'artiste' => '<artiste>',
            'url_image' => '<url_image>',
            'description' => '<description>'
        ];
        
        $this->Oeuvre['properties']['values']->setValue($mock, $values);

        // set_column utilise htmlspecialchars sur la valeur
        $mock->expects($this->exactly(count($values)))
            ->method('htmlspecialchars')
            ->with('value')
            ->willReturn('value');

        // set_column utilise trim sur la valeur
        $mock->expects($this->exactly(count($values)))
            ->method('trim')
            ->with('value')
            ->willReturn('value');

        
        // set_column change la valeur dans le tableau values
        foreach($values as $key => $value){
            $mock->set_column($key, 'value');
            $this->assertEquals('value', $this->Oeuvre['properties']['values']->getValue($mock)[$key]);
        }
    }

    public function test__htmlspecialchars(){
        $oeuvre = new Oeuvre();

        $this->assertEquals('value', $oeuvre->htmlspecialchars('value'));
        $this->assertEquals('&lt;value&gt;', $oeuvre->htmlspecialchars('<value>'));
    }

    public function test__trim(){
        $oeuvre = new Oeuvre();

        $this->assertEquals('value', $oeuvre->trim('value'));
        $this->assertEquals('value', $oeuvre->trim(' value '));
    }

    public function test_to_array(){
        $mock = $this->getMockBuilder(Oeuvre::class)
            ->setConstructorArgs([
                'titre' => 'titre',
                'artiste' => 'artiste',
                'url_image' => 'url_image',
                'description' => 'description'
            ])
            ->setMethods(['get'])
            ->getMock();

        // to_array utilise la fonction get pour ses overrides de valeurs
        $mock->expects($this->exactly(5))
            ->method('get')
            ->withConsecutive(
                ['id'],
                ['titre'],
                ['artiste'],
                ['url_image'],
                ['description']
            )
            ->willReturnOnConsecutiveCalls(
                1,
                '<titre>',
                '<artiste>',
                '<url_image>',
                '<description>'
            );

        // to_array retourne un tableau associatif des valeurs
        $this->assertEquals([
            'id' => 1,
            'titre' => '<titre>',
            'artiste' => '<artiste>',
            'url_image' => '<url_image>',
            'description' => '<description>'
        ], $mock->to_array());
    }

    public function test__to_array_multiple(){
        $instances = [];

        // on crée 5 instances mockées
        for($i = 0; $i < 5; $i++){
            $instances[] = $this->getMockBuilder(Oeuvre::class)
                ->setConstructorArgs([
                    'titre' => 'titre_'.$i,
                    'artiste' => 'artiste_'.$i,
                    'url_image' => 'image_'.$i,
                    'description' => 'description_'.$i
                ])
                ->setMethods(['to_array', 'hydrate'])
                ->getMock();

            // les instances paires sont hydratées
            if($i % 2 == 0){
                $this->Oeuvre['properties']['hydrated']->setValue($instances[$i], true);
            }

            // les id sont définis
            $this->Oeuvre['properties']['values']->setValue($instances[$i], ['id' => $i]);
        }

        $expected = [];
        foreach($instances as $i => $instance){
            // to_array retourne un tableau associatif des valeurs
            $expected[] = [
                'id' => $i,
                'titre' => 'titre_'.$i,
                'artiste' => 'artiste_'.$i,
                'url_image' => 'image_'.$i,
                'description' => 'description_'.$i
            ];

            // to_array est appelée sur chaque instance
            $instance->expects($this->once())
                ->method('to_array')
                ->willReturn($expected[$i]);

            // hydrate est appelée sur les instances impaires (non hydratées)
            if($i % 2 == 1){
                $instance->expects($this->once())
                    ->method('hydrate');
            } else {
                $instance->expects($this->never())
                    ->method('hydrate');
            }
        }

        // to_array_multiple retourne un tableau contenant les tableaux retournés par to_array
        $this->assertEquals($expected, Oeuvre::to_array_multiple($instances));
    }

    function test__fetch(){
        $mock = $this->getMockBuilder(Oeuvre::class)
            ->setMethods(['hydrate', 'unifomizeFilters'])
            ->getMock();

        // appeler fetch initialise la BDD si besoin
        $this->Oeuvre['properties']['bdd']->setValue(null, null);
        $this->Oeuvre['properties']['bdd']->setValue($mock, $this->BDD);

        $mock->fetch();

        $this->assertNotEmpty($this->Oeuvre['properties']['bdd']->getValue($mock));

        // fetch utilise unifomizeFilters pour obtenir les filtres
        $mock->expects($this->once())
            ->method('unifomizeFilters')
            ->willReturn(['id' => 99]);

        $mock->fetch();
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
}