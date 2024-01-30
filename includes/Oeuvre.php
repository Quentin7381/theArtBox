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

    static protected $lastQuery = null;
    protected $values = [];
    protected $hydrated = false;

    public function __construct(...$args) {
        if(!self::$bdd) self::$bdd = BDD::getInstance();

        if(count($args) === 1 && is_array($args[0])) {
            foreach(self::$columns as $key){
                $this->values[$key] = $args[0][$key] ?? null;
            }
        }

        else if(count($args) === 1 && is_object($args[0])) {
            $this->values['id'] = $args[0]->id ?? null;
            $this->set('titre', $args[0]->titre ?? null);
            $this->set('artiste', $args[0]->artiste ?? null);
            $this->set('url_image', $args[0]->url_image ?? null);
            $this->set('description', $args[0]->description ?? null);
        }

        else if(count($args) < 6){
            $this->set('titre', $args[0] ?? null);
            $this->set('artiste', $args[1] ?? null);
            $this->set('url_image', $args[2] ?? null);
            $this->set('description', $args[3] ?? null);
            $this->set('id', $args[4] ?? null);
        }

        else throw new Exception('In '.__CLASS__.'::'.__FUNCTION__.'(): Invalid arguments. Arguments can be an associative array, an object, or positional arguments (titre, artiste, url_image, description, id)');
    }

    public static function from_array($array){
        $instance = new Oeuvre($array);
        return $instance;
    }

    public function get($key){
        if(method_exists($this, 'get_'.$key)) return $this->{'get_'.$key}();
        if(in_array($key, self::$columns)) return $this->get_column($key);
        return $this->$key;
        
    }

    public function __get($key) {
        return $this->get($key);
    }

    public function get_column($key){
        if($this->values[$key] === null && !$this->hydrated) $this->hydrate();
        return $this->values[$key];
    }

    public function get_link(){
        if($this->values['id'] === null) return null;
        return Config::getInstance()->url_root.'view/?id='.$this->values['id'];
    }

    public function get_image(){
        if($this->values['url_image'] === null) return null;
        return Config::getInstance()->url_img.$this->values['url_image'];
    }

    public function set($key, $value){
        if(method_exists($this, 'set_'.$key)) return $this->{'set_'.$key}($value);
        if(in_array($key, self::$columns)) return $this->set_column($key, $value);

        user_error('In '.__CLASS__.'::'.__FUNCTION__.'(): "' . $key . '" is read-only or undefined. Define a setter for '.$key.' or add it to the list of columns');
        return false;
    }

    public function __set($key, $value) {
        return $this->set($key, $value);
    }

    public function set_column($key, $value){
        $this->values[$key] = $value;
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

        $select = 'SELECT * ';

        if(!empty($options['select'])){
            if($options['select'] === 'COUNT') $select = 'SELECT COUNT(*) ';
            else if(is_array($options['select'])){
                $select = 'SELECT ';
                foreach ($options['select'] as $key => $value) {
                    if(!in_array($value, self::$columns)) throw new Exception('In '.__CLASS__.'::'.__FUNCTION__.'(): Invalid argument $options[\'return\']. Array values must match columns names ('.implode(', ', self::$columns).')');
                    $select .= $value.', ';
                }
                $select = substr($select, 0, -2);
                $select .= ' ';
            }
            else if(is_string($options['select']) && in_array($options['select'], self::$columns)) $select = 'SELECT '.$options['select'].' ';
            else throw new Exception('In '.__CLASS__.'::'.__FUNCTION__.'(): Invalid argument $options[\'select\']. String value must match columns names ('.implode(', ', self::$columns).') or implemented functions (\'COUNT\').');
        }

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
        static::$lastQuery = $sql;
        $stmt = self::$bdd->prepare($sql);
        $stmt->execute($params);

        // Récupération des résultats
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if(!empty($options['select'])){
            switch($options['select']){
                case 'COUNT' :
                    return $results[0]['COUNT(*)'];
            }
        }
        
        // Création des instances
        $instances = [];
        foreach ($results as $result) {
            $instances[] = new Oeuvre($result);
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
        if($this->values['id'] === null) return false;
        if($this->hydrated) return true;

        $sql = 'SELECT * FROM '.self::$table.' WHERE id = :id';
        static::$lastQuery = $sql;
        $stmt = self::$bdd->prepare($sql);
        $stmt->execute(['id' => $this->values['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$result) return false;
        
        foreach ($result as $key => $value) {
            if($this->values[$key] === null) $this->values[$key] = $value;
        }

        $this->hydrated = true;

        return true;
    }

    public function save(){
        if($this->values['id'] === null) return $this->insert();
        return $this->update();
    }

    private function insert(){
        $stmt = self::$bdd->prepare('INSERT INTO '.self::$table.' (titre, artiste, url_image, description) VALUES (:titre, :artiste, :url_image, :description)');
        $stmt->execute([
            'titre' => $this->get('titre'),
            'artiste' => $this->get('artiste'),
            'url_image' => $this->get('url_image'),
            'description' => $this->get('description')
        ]);
        $this->values['id'] = self::$bdd->lastInsertId();
        return true;
    }

    private function update(){
        if(!$this->hydrated) $this->hydrate();
        if(!$this->hydrated) $this->insert(); // Si l'instance n'existe pas en base, on l'insère

        $sql = 'UPDATE '.self::$table.' SET titre = :titre, artiste = :artiste, url_image = :url_image, description = :description WHERE id = :id';
        static::$lastQuery = $sql;
        $stmt = self::$bdd->prepare($sql);
        $stmt->execute([
            'id' => $this->get('id'),
            'titre' => $this->get('titre'),
            'artiste' => $this->get('artiste'),
            'url_image' => $this->get('url_image'),
            'description' => $this->get('description')
        ]);
        return true;
    }

    function delete(){
        if(!$this->hydrated) $this->hydrate();
        if(!$this->hydrated) return false;

        $sql = 'DELETE FROM '.self::$table.' WHERE id = :id';
        static::$lastQuery = $sql;
        $stmt = self::$bdd->prepare($sql);
        $stmt->execute(['id' => $this->get('id')]);
        return true;
    }

    public function to_array(){
        return [
            'id' => $this->get('id'),
            'titre' => $this->get('titre'),
            'artiste' => $this->get('artiste'),
            'url_image' => $this->get('url_image'),
            'description' => $this->get('description')
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