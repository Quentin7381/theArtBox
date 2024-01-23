<?php

require_once __DIR__.'/TestSetup.php';

class OeuvresTest extends TestSetup{

    function test__construct(){
        $oeuvre = new Oeuvre('titre', 'artiste', 'url_image', 'description');
        
        // Result object is the expected one
        $this->assertEquals('titre', $oeuvre->titre);
        $this->assertEquals('artiste', $oeuvre->artiste);
        $this->assertEquals('url_image', $oeuvre->url_image);
        $this->assertEquals('description', $oeuvre->description);

        // Constructor sets to null by default
        $oeuvre = new Oeuvre();
        $this->assertEquals(null, $oeuvre->titre);
        $this->assertEquals(null, $oeuvre->artiste);
        $this->assertEquals(null, $oeuvre->url_image);
        $this->assertEquals(null, $oeuvre->link);
        $this->assertEquals(null, $oeuvre->description);
    }

    function test__fromArray(){
        $oeuvre = Oeuvre::from_array([
            'titre' => 'titre',
            'artiste' => 'artiste',
            'url_image' => 'url_image',
            'description' => 'description'
        ]);
        
        // Result object is the expected one
        $this->assertEquals('titre', $oeuvre->titre);
        $this->assertEquals('artiste', $oeuvre->artiste);
        $this->assertEquals('url_image', $oeuvre->url_image);
        $this->assertEquals('description', $oeuvre->description);
    }

    function test__fetch(){
        // Empty fetch would fetch all entries
        $oeuvres = Oeuvre::fetch();
        $this->assertEquals(3, count($oeuvres));
        
        // Fetch with filters
        $oeuvres = Oeuvre::fetch([
            'titre' => 'titre_1'
        ]);
        $this->assertEquals(1, count($oeuvres));

        $oeuvres = Oeuvre::fetch([
            'artiste' => 'artiste_1'
        ]);
        $this->assertEquals(2, count($oeuvres));

        $oeuvres = Oeuvre::fetch([
            'titre' => 'titre_3',
            'artiste' => 'artiste_1'
        ]);
        $this->assertEquals(1, count($oeuvres));
        $this->assertEquals('titre_3', $oeuvres[0]->titre);

        // Fetch with options

        // order_by
        $oeuvres = Oeuvre::fetch([], [
            'order_by' => 'titre'
        ]);
        $this->assertEquals('titre_1', $oeuvres[0]->titre);
        $this->assertEquals('titre_2', $oeuvres[1]->titre);

        // order
        $oeuvres = Oeuvre::fetch([], [
            'order_by' => 'titre',
            'order' => 'DESC'
        ]);
        $this->assertEquals('titre_3', $oeuvres[0]->titre);
        $this->assertEquals('titre_2', $oeuvres[1]->titre);

        // limit
        $oeuvres = Oeuvre::fetch([], [
            'limit' => 1
        ]);
        $this->assertEquals(1, count($oeuvres));

        // offset
        $oeuvres = Oeuvre::fetch([], [
            'limit' => 1,
            'offset' => 1
        ]);
        $this->assertEquals(1, count($oeuvres));
        $this->assertEquals('titre_2', $oeuvres[0]->titre);
    }

    function test__uniformizeFilters(){
        // Using simple values
        $filters = [
            'id' => 1,
            'titre' => 'Bla',
            'artiste' => 'Bla',
            'url_image' => 'Bla',
            'link' => 'Bla',
            'description' => 'Bla'
        ];
        $filters = Oeuvre::uniformizeFilters($filters);
        $this->assertEquals([
            'id' => [
                'value' => 1,
                'operator' => '='
            ],
            'titre' => [
                'value' => 'Bla',
                'operator' => '='
            ],
            'artiste' => [
                'value' => 'Bla',
                'operator' => '='
            ],
            'url_image' => [
                'value' => 'Bla',
                'operator' => '='
            ],
            'link' => [
                'value' => 'Bla',
                'operator' => '='
            ],
            'description' => [
                'value' => 'Bla',
                'operator' => '='
            ]
        ], $filters);

        // Using arrays of values
        $filters = [
            'id' => [1, 2, 3],
            'titre' => ['Bla', 'Bla', 'Bla'],
            'artiste' => ['Bla', 'Bla', 'Bla'],
            'url_image' => ['Bla', 'Bla', 'Bla'],
            'link' => ['Bla', 'Bla', 'Bla'],
            'description' => ['Bla', 'Bla', 'Bla']
        ];
        $filters = Oeuvre::uniformizeFilters($filters);
        $this->assertEquals([
            'id' => [
                'value' => [1, 2, 3],
                'operator' => 'IN'
            ],
            'titre' => [
                'value' => ['Bla', 'Bla', 'Bla'],
                'operator' => 'IN'
            ],
            'artiste' => [
                'value' => ['Bla', 'Bla', 'Bla'],
                'operator' => 'IN'
            ],
            'url_image' => [
                'value' => ['Bla', 'Bla', 'Bla'],
                'operator' => 'IN'
            ],
            'link' => [
                'value' => ['Bla', 'Bla', 'Bla'],
                'operator' => 'IN'
            ],
            'description' => [
                'value' => ['Bla', 'Bla', 'Bla'],
                'operator' => 'IN'
            ]
        ], $filters);

        // Using complex structure with value and operator
        $filters = [
            'id' => [
                'value' => [1, 2, 3],
                'operator' => 'NOT IN'
            ],
            'titre' => [
                'value' => ['Bla', 'Bla', 'Bla'],
                'operator' => 'IN'
            ],
            'artiste' => [
                'value' => 1,
                'operator' => '='
            ],
            'url_image' => [
                'value' => 2,
                'operator' => '>='
            ],
            'link' => [
                'value' => 3,
                'operator' => '<='
            ],
            'description' => [
                'value' => 'baggels',
                'operator' => 'LIKE'
            ]
        ];
        $filters = Oeuvre::uniformizeFilters($filters);
        $this->assertEquals([
            'id' => [
                'value' => [1, 2, 3],
                'operator' => 'NOT IN'
            ],
            'titre' => [
                'value' => ['Bla', 'Bla', 'Bla'],
                'operator' => 'IN'
            ],
            'artiste' => [
                'value' => 1,
                'operator' => '='
            ],
            'url_image' => [
                'value' => 2,
                'operator' => '>='
            ],
            'link' => [
                'value' => 3,
                'operator' => '<='
            ],
            'description' => [
                'value' => 'baggels',
                'operator' => 'LIKE'
            ]
        ], $filters);
    }

    function test__set(){
        // Setting properties that are not collumns or do not implement a setter will fail

        $oeuvre = new Oeuvre();
        @$oeuvre->hydrated = true;
        $this->assertEquals(false, $oeuvre->hydrated);

        @$oeuvre->not_a_column = true;
        $this->assertEquals(null, @$oeuvre->not_a_column);
    }

    function test__hydrate(){
        // Getting an existing id
        $oeuvre = Oeuvre::fetch([], ['limit' => 1])[0];
        $id = $oeuvre->id;

        // Hydrating will fill the object with data from the database
        $oeuvre = new Oeuvre();
        $oeuvre->id = $id;
        $oeuvre->hydrate();

        $this->assertEquals('titre_1', $oeuvre->titre);
        $this->assertEquals('artiste_1', $oeuvre->artiste);
        $this->assertEquals('image_1', $oeuvre->url_image);
        $this->assertEquals('description_1', $oeuvre->description);

        // Hydrating an already hydrated object will do nothing
        $oeuvre->titre = 'titre_2';
        $oeuvre->hydrate();

        $this->assertEquals('titre_2', $oeuvre->titre);

        // Hydrated object will keep their values if already set
        $oeuvre = new Oeuvre();
        $oeuvre->id = $id;
        $oeuvre->titre = 'titre_3';
        $oeuvre->hydrate();

        $this->assertEquals('titre_3', $oeuvre->titre);

        // Hydrating an object with an invalid id will do nothing
        $oeuvre = new Oeuvre();
        $oeuvre->titre = 'titre_3';
        $oeuvre->id = -1;
        $oeuvre->hydrate();

        $this->assertEquals('titre_3', $oeuvre->titre);
    }

    function test__save(){
        // save sans id effecture un insert et ajoute un l'id
        $oeuvre = new Oeuvre();
        $oeuvre->titre = 'titre_4';
        $oeuvre->artiste = 'artiste_4';
        $oeuvre->url_image = 'image_4';
        $oeuvre->description = 'description_4';
        $oeuvre->save();

        $this->assertNotNull($oeuvre->id);

        $fetch = Oeuvre::fetch([
            'id' => $oeuvre->id
        ]);
        $this->assertEquals(1, count($fetch));
        $this->assertEquals('titre_4', $fetch[0]->titre);
        $this->assertEquals('artiste_4', $fetch[0]->artiste);
        $this->assertEquals('image_4', $fetch[0]->url_image);
        $this->assertEquals('description_4', $fetch[0]->description);

        // save avec id effectue un update
        $existingID = Oeuvre::fetch([], ['limit' => 1])[0]->id;
        $oeuvre = new Oeuvre();
        $oeuvre->titre = 'titre_5';
        $oeuvre->id = $existingID;

        $oeuvre->save();

        $fetch = Oeuvre::fetch([
            'id' => $existingID
        ]);
        $this->assertEquals(1, count($fetch));
        $this->assertEquals('titre_5', $fetch[0]->titre);
        $this->assertEquals('artiste_1', $fetch[0]->artiste);
        $this->assertEquals('image_1', $fetch[0]->url_image);
        $this->assertEquals('description_1', $fetch[0]->description);

        // save avec id inexistant effectue un insert
        $oeuvre = new Oeuvre();
        $oeuvre->titre = 'titre_6';
        $oeuvre->artiste = 'artiste_6';
        $oeuvre->url_image = 'image_6';
        $oeuvre->description = 'description_6';
        $oeuvre->id = -1;

        $oeuvre->save();

        $this->assertNotNull($oeuvre->id);

        $fetch = Oeuvre::fetch([
            'id' => $oeuvre->id
        ]);

        $this->assertEquals(1, count($fetch));
        $this->assertEquals('titre_6', $fetch[0]->titre);
        $this->assertEquals('artiste_6', $fetch[0]->artiste);
        $this->assertEquals('image_6', $fetch[0]->url_image);
        $this->assertEquals('description_6', $fetch[0]->description);
    }
}