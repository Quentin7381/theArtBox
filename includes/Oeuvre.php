<?php

class Oeuvre {
    static protected $instances = [];
    static protected $table = 'oeuvres';
    static protected $bdd = null;
    static protected $columns = [
        'id',
        'titre',
        'artiste',
        'url_image',
        'description'
    ];
    static protected $columns_mutators = [
        'link',
        'image',
    ];
    protected $id;
    protected $titre;
    protected $artiste;
    protected $url_image;
    protected $image;
    protected $link;
    protected $description;
    protected $hydrated = false;

    public function __construct($titre = null, $artiste = null, $url_image = null, $description = null) {
        if(!self::$bdd) self::$bdd = BDD::getInstance();
        $this->__set('titre', $titre);
        $this->__set('artiste', $artiste);
        $this->__set('url_image', $url_image);
        $this->__set('description', $description);
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
        if(in_array($key, self::$columns)) return $this->get_column($key);
        return $this->$key;
    }

    public function get_column($key){
        if($this->$key === null && !$this->hydrated) $this->hydrate();
        return $this->$key;
    }

    public function get_link(){
        if($this->id === null) return null;
        return Config::getInstance()->url_root.'view/?id='.$this->id;
    }

    public function get_image(){
        if($this->url_image === null) $this->hydrate();
        if($this->url_image === null) return null;
        return Config::getInstance()->url_root.'img/'.$this->url_image;
    }

    public function get_title(){
        if($this->titre === null) return 'Sans titre';
        return $this->titre;
    }

    public function get_artist(){
        if($this->artiste === null) return 'Anonyme';
        return $this->artiste;
    }

    public function get_description(){
        if($this->description === null) return 'Sans description';
        return $this->description;
    }

    public function __set($key, $value) {
        if(method_exists($this, 'set_'.$key)) return $this->{'set_'.$key}($value);
        if(in_array($key, self::$columns)) return $this->set_column($key, $value);

        user_error('In '.__CLASS__.'::'.__FUNCTION__.'(): "' . $key . '" is read-only or undefined. Define a setter for '.$key.' or add it to the list of columns');
        return false;
    }

    public function set_column($key, $value){
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
     *     'url_image' => 'Bla',
     *     'link' => 'Bla',
     *     'description' => 'Bla'
     * ]
     *
     * 2. Multiple values structure:
     * [
     *     'id' => [1, 2, 3],
     *     'titre' => ['Bla', 'Bla', 'Bla'],
     *     'artiste' => ['Bla', 'Bla', 'Bla'],
     *     'url_image' => ['Bla', 'Bla', 'Bla'],
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
     *     'url_image' => [
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
                'url_image' => 'Bla',
                'link' => 'Bla',
                'description' => 'Bla'
            ]
            OU
            [
                'id' => [1, 2, 3],
                'titre' => ['Bla', 'Bla', 'Bla'],
                'artiste' => ['Bla', 'Bla', 'Bla'],
                'url_image' => ['Bla', 'Bla', 'Bla'],
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
            ]
        */
    public static function fetch($filters = [], $options = []){
        if(!self::$bdd) self::$bdd = BDD::getInstance();
        
        // Ajout des filtres
        $filters = self::uniformizeFilters($filters);

        if(!empty($options['return']) && $options['return'] === 'count') {
            $options['limit'] = null;
            $options['offset'] = null;
            $select = 'SELECT COUNT(*) ';
        }
        else $select = 'SELECT * ';

        $sql = $select;
        $sql .= 'FROM '.self::$table;
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

        if(!empty($options['return'])){
            switch($options['return']){
                case 'count' :
                    return $results[0]['COUNT(*)'];
                case 'ids' :
                    return array_map(function ($result) {
                        return $result['id'];
                    }, $results);
            }
        }
        
        // Création des instances
        $instances = [];
        foreach ($results as $result) {
            $instance = new Oeuvre();
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

    public function hydrate(){
        if($this->id === null) return false;
        if($this->hydrated) return true;

        $stmt = self::$bdd->prepare('SELECT * FROM '.self::$table.' WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$result) return false;
        
        foreach ($result as $key => $value) {
            if($this->$key === null) $this->$key = $value;
        }

        $this->hydrated = true;

        return true;
    }

    public function save(){
        if($this->id === null) return $this->insert();
        return $this->update();
    }

    private function insert(){
        $stmt = self::$bdd->prepare('INSERT INTO '.self::$table.' (titre, artiste, url_image, description) VALUES (:titre, :artiste, :url_image, :description)');
        $stmt->execute([
            'titre' => $this->titre,
            'artiste' => $this->artiste,
            'url_image' => $this->url_image,
            'description' => $this->description
        ]);
        $this->id = self::$bdd->lastInsertId();
        return true;
    }

    private function update(){
        if(!$this->hydrated) $this->hydrate();
        if(!$this->hydrated) $this->insert(); // Si l'instance n'existe pas en base, on l'insère

        $stmt = self::$bdd->prepare('UPDATE '.self::$table.' SET titre = :titre, artiste = :artiste, url_image = :url_image, description = :description WHERE id = :id');
        $stmt->execute([
            'id' => $this->id,
            'titre' => $this->titre,
            'artiste' => $this->artiste,
            'url_image' => $this->url_image,
            'description' => $this->description
        ]);
        return true;
    }

    function delete(){
        if(!$this->hydrated) $this->hydrate();
        if(!$this->hydrated) return false;

        $stmt = self::$bdd->prepare('DELETE FROM '.self::$table.' WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
        return true;
    }

    public function to_array(){
        return [
            'id' => $this->id,
            'titre' => $this->titre,
            'artiste' => $this->artiste,
            'url_image' => $this->url_image,
            'description' => $this->description
        ];
    }

    public static function to_array_multiple($instances){
        $array = [];
        foreach ($instances as $instance) {
            if(!$instance instanceof static) throw new Exception('In '.__CLASS__.'::'.__FUNCTION__.'(): $instances must be an array of '.static::class.' instances');
            if(!$instance->hydrated) $instance->hydrate();
            $array[] = $instance->to_array();
        }
        return $array;
    }
}