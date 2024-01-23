<?php

class Oeuvre {

    static protected $instances = [];
    static protected $table = 'oeuvres';
    static protected $bdd = null;

    protected $id;
    protected $titre;
    protected $artiste;
    protected $image;
    protected $link;
    protected $description;

    public function __construct($titre = null, $artiste = null, $image = null, $link = null, $description = null) {
        if(!self::$bdd) self::$bdd = BDD::getInstance();
        $this->__set('titre', $titre);
        $this->__set('artiste', $artiste);
        $this->__set('image', $image);
        $this->__set('link', $link);
        $this-> __set('description', $description);
    }

    public static function from_array($array){
        $instance = new Oeuvre();
        foreach ($array as $key => $value) {
            $instance->$key = $value;
        }
        return $instance;
    }

    public function __get($key) {
        if(method_exists($this, 'get_'.$key)) return $this->{'get_'.$key}();
        return $this->$key;
    }

    public function __set($key, $value) {
        if(method_exists($this, 'set_'.$key)) return $this->{'set_'.$key}($value);
        $this->$key = $value;
    }

    // ----- DB methods -----
    /**
     * Retourne un tableau d'instances de Oeuvre correspondant aux filtres et options passés en paramètres
     * 
     * @param array $filters Filtres à appliquer à la requête
     *
     * 1. Single value structure:
     * [
     *     'id' => 1,
     *     'titre' => 'Bla',
     *     'artiste' => 'Bla',
     *     'image' => 'Bla',
     *     'link' => 'Bla',
     *     'description' => 'Bla'
     * ]
     *
     * 2. Multiple values structure:
     * [
     *     'id' => [1, 2, 3],
     *     'titre' => ['Bla', 'Bla', 'Bla'],
     *     'artiste' => ['Bla', 'Bla', 'Bla'],
     *     'image' => ['Bla', 'Bla', 'Bla'],
     *     'link' => ['Bla', 'Bla', 'Bla'],
     *     'description' => ['Bla', 'Bla', 'Bla']
     * ]
     *
     * 3. Complex structure with value and operator:
     * [
     *     'id' => [
     *         'value' => [1, 2, 3],
     *         'operator' => 'IN'
     *     ],
     *     'titre' => [
     *         'value' => ['Bla', 'Bla', 'Bla'],
     *         'operator' => 'IN'
     *     ],
     *     'artiste' => [
     *         'value' => ['Bla', 'Bla', 'Bla'],
     *         'operator' => 'IN'
     *     ],
     *     'image' => [
     *         'value' => ['Bla', 'Bla', 'Bla'],
     *         'operator' => 'IN'
     *     ],
     *     'link' => [
     *         'value' => ['Bla', 'Bla', 'Bla'],
     *         'operator' => 'IN'
     *     ],
     *     'description' => [
     *         'value' => ['Bla', 'Bla', 'Bla'],
     *         'operator' => 'IN'
     *     ]
     * ]
     * 
     * @param array $options Options à appliquer à la requête (order_by, limit, offset)
     */
    /*
            Structure des filtres :
            [
                'id' => 1,
                'titre' => 'Bla',
                'artiste' => 'Bla',
                'image' => 'Bla',
                'link' => 'Bla',
                'description' => 'Bla'
            ]
            OU
            [
                'id' => [1, 2, 3],
                'titre' => ['Bla', 'Bla', 'Bla'],
                'artiste' => ['Bla', 'Bla', 'Bla'],
                'image' => ['Bla', 'Bla', 'Bla'],
                'link' => ['Bla', 'Bla', 'Bla'],
                'description' => ['Bla', 'Bla', 'Bla']
            ]
            OU
            [
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
            ]
        */
    public static function fetch($filters = [], $options = []){
        if(!self::$bdd) self::$bdd = BDD::getInstance();
        
        // Ajout des filtres
        $filters = self::uniformizeFilters($filters);

        $sql = 'SELECT * FROM '.self::$table;
        $sql .= ' WHERE 1';
        $params = [];
        foreach ($filters as $key => $filter) {
            $sql .= ' AND '.$key.' '.$filter['operator'].' (';
            if(!is_array($filter['value'])) $filter['value'] = [$filter['value']];
            foreach ($filter['value'] as $i => $value) {
                $sql .= ':param_'.$key.'_'.$i.', ';
                $params['param_'.$key.'_'.$i] = $value;
            }
            $sql = substr($sql, 0, -2);
            $sql .= ')';
        }

        // Ajout des options
        if(!empty($options['order_by'])) $sql .= ' ORDER BY '.$options['order_by'];
        if(!empty($options['limit'])) $sql .= ' LIMIT '.$options['limit'];
        if(!empty($options['offset'])) $sql .= ' OFFSET '.$options['offset'];
        if(!empty($options['order'])) $sql .= ' '.$options['order'];

        // Exécution de la requête
        $stmt = self::$bdd->prepare($sql);
        $stmt->execute($params);

        // Récupération des résultats
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Création des instances
        $instances = [];
        foreach ($results as $result) {
            $instance = new Oeuvre($result['titre'], $result['artiste'], $result['url_image'], $result['description']);
            $instance->id = $result['id'];
            $instances[] = $instance;
        }

        return $instances;
    }

    public static function uniformizeFilters($filters){
        $uniformizedFilters = [];
        foreach ($filters as $key => $filter) {
            if(
                // Le filtre est un tableau contenant un index 'value' (et optionnellement un index 'operator')
                is_array($filter)
                && isset($filter['value'])
            ) {
                $uniformizedFilters[$key] = $filter;
                continue;
            }

            if(
                // Le filtre est un tableau sans indexes, contenant une suite de valeurs
                is_array($filter)
            ) {
                $uniformizedFilters[$key] = [
                    'value' => $filter,
                    'operator' => 'IN'
                ];
                continue;
            }

            // Le filtre est une valeur simple
            $uniformizedFilters[$key] = [
                'value' => $filter,
                'operator' => '='
            ];
        }
        return $uniformizedFilters;
    }
}