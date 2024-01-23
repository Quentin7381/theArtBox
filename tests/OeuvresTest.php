<?php

require_once __DIR__.'/TestSetup.php';

class OeuvresTest extends TestSetup{

    function test__construct(){
        $oeuvre = new Oeuvre('titre', 'artiste', 'image', 'link', 'description');
        
        // Result object is the expected one
        $this->assertEquals('titre', $oeuvre->titre);
        $this->assertEquals('artiste', $oeuvre->artiste);
        $this->assertEquals('image', $oeuvre->image);
        $this->assertEquals('link', $oeuvre->link);
        $this->assertEquals('description', $oeuvre->description);

        // Constructor sets to null by default
        $oeuvre = new Oeuvre();
        $this->assertEquals(null, $oeuvre->titre);
        $this->assertEquals(null, $oeuvre->artiste);
        $this->assertEquals(null, $oeuvre->image);
        $this->assertEquals(null, $oeuvre->link);
        $this->assertEquals(null, $oeuvre->description);
    }

    function test__fromArray(){
        $oeuvre = Oeuvre::from_array([
            'titre' => 'titre',
            'artiste' => 'artiste',
            'image' => 'image',
            'link' => 'link',
            'description' => 'description'
        ]);
        
        // Result object is the expected one
        $this->assertEquals('titre', $oeuvre->titre);
        $this->assertEquals('artiste', $oeuvre->artiste);
        $this->assertEquals('image', $oeuvre->image);
        $this->assertEquals('link', $oeuvre->link);
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
            'image' => 'Bla',
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
            'image' => [
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
            'image' => ['Bla', 'Bla', 'Bla'],
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
            'image' => [
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
            'image' => [
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
            'image' => [
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
}