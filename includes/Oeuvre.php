<?php

use ExceptionFactory as EF;

/**
 * Représente une oeuvre d'art
 *
 * ----- ACCESSORS -----
 * Note : les fonctions get et set peuvent être override en implémentant des méthodes get_$key et set_$key
 * Plus d'informations dans la documentation de ces méthodes
 *
 * @method mixed get(string $key) Renvoie la valeur de la propriété $key
 * @method mixed get_column(string $key) Renvoie la valeur de la colonne $key
 * @method string get_link() Renvoie le lien vers la page de l'oeuvre
 * @method string get_image() Renvoie le lien vers l'image de l'oeuvre
 *
 * @method void set(string $key, mixed $value) Associe la valeur $value à la propriété $key
 * @method void set_column(string $key, mixed $value) Associe la valeur $value à la colonne $key
 *
 *----- IMPORT / EXPORT -----
 * @method array to_array() Renvoie un tableau associatif contenant les propriétés de l'instance
 * @method static array to_array_multiple() Applique la méthode to_array() à un tableau d'instances
 * @method static Oeuvre from_array() Crée une instance de Oeuvre à partir d'un tableau associatif
 *
 * ----- DB METHODS -----
 * @method bool hydrate() Hydrate l'instance avec les données de la base de données
 * @method bool save() Enregistre l'instance en base de données
 * @method bool delete() Supprime l'instance de la base de données
 * @method static array fetch() Renvoie un tableau d'instances de Oeuvre correspondant aux filtres en paramètres
 * @method static array uniformizeFilters() Rends les filtres compatibles avec la méthode fetch()
 *
 * ----- TOOL METHODS -----
 *
 */
class Oeuvre
{
    /**
     * Nom de la table en base de données
     *
     * @var string
     */
    protected static $table = 'oeuvres';

    /**
     * Instance singleton de BDD
     *
     * @var BDD
     */
    protected static $bdd = null;

    /**
     * Liste des colonnes de la table en base de données
     *
     * @var array
     */
    protected static $columns = [
        'id',
        'titre',
        'artiste',
        'url_image',
        'description'
    ];

    /**
     * Liste des mutateurs de colonnes
     *
     * Ces mutateurs seront appelés automatiquement lors de l'accès à la propriété correspondante
     * Exemple : $oeuvre->link
     *
     * @var array
     */
    protected static $columns_mutators = [
        'link',
        'image',
    ];

    /**
     * Valeurs de l'instance
     *
     * @var array
     */
    public $values = [];

    /**
     * Indique si l'instance a été hydratée
     *
     * @var bool
     */
    protected $hydrated = false;

    protected $sql = null;

    /**
     * Constructeur de la classe Oeuvre
     *
     * Accepte plusieurs types d'arguments :
     * 1. Une liste d'arguments positionnels (titre, artiste, url_image, description, id)
     * 2. Un tableau associatif contenant les propriétés de l'instance
     * 3. Un objet contenant les propriétés de l'instance
     *
     * @param mixed ...$args Arguments à passer au constructeur
     * @throws Exception Si les arguments passés ne sont pas valides (trop nombreux, mauvais type)
     */
    public function __construct(...$args)
    {
        // Récupération de l'instance singleton de BDD
        self::initBDD();

        // Traitement des arguments en tableau associatif
        if (count($args) === 1 && is_array($args[0])) {
            foreach (self::$columns as $key) {
                $this->values[$key] = $args[0][$key] ?? null;
            }
        }

        // Traitement des arguments en objet
        elseif (count($args) === 1 && is_object($args[0])) {
            $this->set('id', $args[0]->id ?? null);
            $this->set('titre', $args[0]->titre ?? null);
            $this->set('artiste', $args[0]->artiste ?? null);
            $this->set('url_image', $args[0]->url_image ?? null);
            $this->set('description', $args[0]->description ?? null);
        }

        // Traitement des arguments positionnels
        elseif (count($args) < 6) {
            $this->set('titre', $args[0] ?? null);
            $this->set('artiste', $args[1] ?? null);
            $this->set('url_image', $args[2] ?? null);
            $this->set('description', $args[3] ?? null);
            $this->set('id', $args[4] ?? null);
        } else {
            throw EF::argument_wrong_type(
                'args',
                $args,
                ['associative array', 'object', 'positionnal strings and ints'],
                'Arguments can be an associative array, an object, or positional arguments' .
                '(titre, artiste, url_image, description, id)'
            );
        }
    }

    // ----- ACCESSORS -----

    /**
     * Renvoie la valeur de la propriété $key
     *
     * Si une méthode get_$key existe, elle sera appelée à la place
     * Si la propriété $key est une colonne de la table en base de données,
     * la méthode get_column($key) sera appelée à la place
     */
    public function get($key)
    {
        if (method_exists($this, 'get_' . $key)){
            return $this->{'get_' . $key}();
        }

        if (in_array($key, self::$columns)){
            return $this->get_column($key);
        }
        return $this->$key;
    }

    public function __get($key)
    {
        return $this->get($key);
    }

    /**
     * Renvoie la valeur de la propriété $key
     *
     * Si la propriété $key est nulle, hydrate l'instance pour tenter de la récupérer
     *
     * @param string $key Nom de la propriété
     */
    public function get_column($key)
    {
        if ($this->values[$key] === null && !$this->hydrated) {
            $this->hydrate();
        }
        return $this->values[$key];
    }

    /**
     * Renvoie le lien complet vers l'affichage en détail de l'oeuvre
     *
     * Utilise la configuration de l'application pour récupérer l'URL racine
     *
     * @return string Lien vers l'image de l'oeuvre
     */
    public function get_link(): ?string
    {
        if ($this->values['id'] === null) {
            return null;
        }
        return Config::getInstance()->url_root . 'view/?id=' . $this->values['id'];
    }

    /**
     * Renvoie le lien complet vers l'image de l'oeuvre
     *
     * Utilise la configuration de l'application pour récupérer l'URL des images
     *
     * @return string Lien vers l'image de l'oeuvre
     */
    public function get_image(): ?string
    {
        if ($this->values['url_image'] === null && !$this->hydrated) {
            $this->hydrate();
        }

        if ($this->values['url_image'] === null) {
            return null;
        }

        return Config::getInstance()->url_img . $this->values['url_image'];
    }

    /**
     * Associe la valeur $value à la propriété $key
     *
     * Si une méthode set_$key existe, elle sera appelée à la place
     * Si la propriété $key est une colonne de la table en base de données,
     * la méthode set_column($key, $value) sera appelée à la place
     *
     * @param string $key Nom de la propriété
     * @param mixed $value Valeur à associer à la propriété
     * @return bool Succès de l'opération
     * @warning Si la propriété $key n'est pas une colonne et n'a pas de fonction set_$key, une alerte sera émise
     */
    public function set($key, $value)
    {
        if (method_exists($this, 'set_' . $key)) {
            return $this->{'set_' . $key}($value);
        }
        if (in_array($key, self::$columns)) {
            return $this->set_column($key, $value);
        }

        user_error(
            'In ' . __CLASS__ . '::' . __FUNCTION__ . '(): "' .
            $key . '" is read-only or undefined. Define a setter for ' . $key . ' or add it to the list of columns'
        );
        return false;
    }

    public function __set($key, $value)
    {
        return $this->set($key, $value);
    }

    /**
     * Associe la valeur $value à la propriété $key
     *
     * Est appelée pour les valeurs de colonnes de la table en base de données
     * Applique htmlspecialchars() et trim() à la valeur avant de l'associer
     *
     * @param string $key Nom de la propriété
     * @param mixed $value Valeur à associer à la propriété
     */
    public function set_column($key, $value)
    {
        $value = $this->htmlspecialchars($value);
        $value = $this->trim($value);

        $this->values[$key] = $value;
    }

    public function trim($value)
    {
        return trim($value);
    }

    public function htmlspecialchars($value)
    {
        return htmlspecialchars((string)$value);
    }

    // ----- IMPORT / EXPORT -----

    /**
     * @deprecated Implémenté par le constructeur
     *
     * Crée une instance de Oeuvre à partir d'un tableau associatif
     *
     * @param array $args Arguments à passer au constructeur
     * @return Oeuvre
     */
    public static function from_array($array): Oeuvre
    {
        return new Oeuvre($array);
    }

    /**
     * Crée un tableau associatif contenant les propriétés de l'instance
     *
     * @return array Tableau associatif contenant les propriétés de l'instance
     */
    public function to_array(): array
    {
        return [
            'id' => $this->get('id'),
            'titre' => $this->get('titre'),
            'artiste' => $this->get('artiste'),
            'url_image' => $this->get('url_image'),
            'description' => $this->get('description')
        ];
    }

    /**
     * Crée de multiples tableaux associatifs contenant les propriétés des instances
     *
     * @param array $instances Tableau d'instances de Oeuvre
     * @return array Tableau associatif contenant les propriétés de l'instance
     * @throws Exception Si $instances n'est pas un tableau d'instances de Oeuvre
     */
    public static function to_array_multiple($instances): array
    {
        $array = [];
        foreach ($instances as $instance) {
            if (!$instance instanceof static) {
                throw EF::instance_wrong_parameter(
                    'instances',
                    $instances,
                    'array of ' . static::class . ' instances',
                );
            }
            if (!$instance->hydrated) {
                $instance->hydrate();
            }
            $array[] = $instance->to_array();
        }
        return $array;
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
    public static function fetch($filters = [], $options = [])
    {
        self::initBDD();

        // Ajout des filtres
        $filters = self::uniformizeFilters($filters);

        $sql = new Querry();
        $sql->select($options['select'] ?? self::$columns);
        $sql->from(self::$table);
        
        $filters = self::uniformizeFilters($filters);
        $sql->where($filters, false);

        // Ajout des options
        if (!empty($options['order_by'])) {
            $sql->order_by($options['order_by']);
        }
        if (!empty($options['limit'])) {
            $sql->limit($options['limit']);
        }
        if(!empty($options['offset'])){
            $sql->offset($options['offset']);
        }
        if (!empty($options['order'])) {
            $sql->order_by($options['order']);
        }

        $sql = $sql->print();

        // Exécution de la requête
        $stmt = self::$bdd->prepare($sql);

        try{
            $stmt->execute();
        } catch (PDOException $e){
            throw EF::pdo_invalid_query($sql, $params, null, $e);
        }

        // Récupération des résultats
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($options['select']) && $options['select'] === 'COUNT(*)') {
            return $results[0]['COUNT(*)'];
        }

        // Création des instances
        $instances = [];
        foreach ($results ?? [] as $result) {
            $instances[] = new Oeuvre($result);
        }

        return $instances;
    }

    /**
     * Uniformise les filtres passés en paramètres pour les rendre compatibles avec la méthode fetch()
     *
     * @param array $filters Filtres à uniformiser
     *
     * @return array Filtres uniformisés
     */
    public static function uniformizeFilters(array $filters)
    {
        $uniformizedFilters = [];
        foreach ($filters as $column => $filter) {
            $value;
            $operator;
            $connector;

            if (
                // Le filtre est un tableau contenant un index 'value' (et optionnellement un index 'operator')
                is_array($filter)
                && isset($filter['value'])
            ) {
                $value = $filter['value'];
                $operator = $filter['operator'] ?? '=';
                $connector = $filter['connector'] ?? 'AND';
                $column = $filter['column'] ?? $column;
            }

            if(
                // Le filtre est un objet contenant un parametre 'value' (et optionellement un parametre 'operator')
                is_object($filter)
                && isset($filter->value)
            ){
                $value = $filter->value;
                $operator = $filter->operator ?? '=';
                $connector = $filter->connector ?? 'AND';
                $column = $filter->column ?? $column;
            }

            elseif (
                // Le filtre est un tableau sans indexes, contenant une suite de valeurs
                is_array($filter)
            ) {
                $value = '(' . implode(', ', $filter) . ')';
                $operator = 'IN';
            }

            // Le filtre est une valeur simple
            else{
                $value = $filter;
            }

            // Préparation des arguments
            $arguments = [$column, $value];
            
            // Les opérateurs et connecteurs sont optionnels
            if(isset($operator)){
                $arguments[] = $operator;
            }
            if(isset($connector)){
                $arguments[] = $connector;
            }

            // Création de l'instance de Condition
            $uniformizedFilters[] = new Condition(...$arguments);
        }

        return $uniformizedFilters;
    }

    /**
     * Hydrate l'instance avec les données de la base de données
     *
     * La propriété id doit être définie pour que l'hydratation fonctionne
     *
     * @return bool Succès de l'opération
     */
    public function hydrate()
    {
        if ($this->values['id'] === null) {
            return false;
        }
        if ($this->hydrated) {
            return true;
        }

        $this->sql = new Querry();
        $this->sql->select(self::$columns);
        $this->sql->from(self::$table);
        $filters = self::uniformizeFilters(['id' => 'id']);
        $this->sql->where($filters, true);

        $sql = $this->sql->print();

        $stmt = self::$bdd->prepare($sql);
        $stmt->execute(['id' => $this->values['id']]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$result) {
            return false;
        }

        foreach ($result as $key => $value) {
            if (empty($this->values[$key])) {
                $this->values[$key] = $value;
            }
        }

        $this->hydrated = true;

        return true;
    }

    /**
     * Enregistre l'instance en base de données
     *
     * Si l'instance n'existe pas en base, elle sera insérée
     * Sinon, elle sera mise à jour
     *
     * @return bool Succès de l'opération
     */
    public function save()
    {
        if ($this->values['id'] === null) {
            return $this->insert();
        }
        return $this->update();
    }

    /**
     * Insère l'instance en base de données
     *
     * @return bool Succès de l'opération
     */
    private function insert()
    {
        $this->sql = new Querry();
        $this->sql->insert_into(self::$table);

        $columns = self::$columns;
        // On retire la colonne id pour l'insertion
        unset($columns[array_search('id', $columns)]);
        // Les clés (identifiants des colonnes) et les valeurs (noms des variables) sont les mêmes
        $columns = array_combine($columns, $columns);

        $this->sql->values($columns);

        $sql = $this->sql->print();
        $stmt = self::$bdd->prepare($sql);
        $success = $stmt->execute([
            'titre' => $this->get('titre'),
            'artiste' => $this->get('artiste'),
            'url_image' => $this->get('url_image'),
            'description' => $this->get('description')
        ]);
        $this->values['id'] = self::$bdd->lastInsertId();
        return $success;
    }

    /**
     * Met à jour l'instance en base de données
     *
     * @return bool Succès de l'opération
     */
    protected function update()
    {
        if (!$this->hydrated) {
            $this->hydrate();
        }
        // Si l'instance n'existe pas en base, ce n'est pas une update mais une insertion
        if (!$this->hydrated) {
            $this->insert();
        }

        $this->sql = new Querry();
        $this->sql->update(self::$table);
        $this->sql->set(['titre', 'artiste', 'url_image', 'description']);
        $filters = self::uniformizeFilters(['id' => 'id']);
        $this->sql->where($filters);

        $sql = $this->sql->print();

        $stmt = self::$bdd->prepare($sql);

        $success = $stmt->execute([
            'id' => $this->get('id'),
            'titre' => $this->get('titre'),
            'artiste' => $this->get('artiste'),
            'url_image' => $this->get('url_image'),
            'description' => $this->get('description')
        ]);

        return $success;
    }

    /**
     * Supprime l'instance de la base de données
     *
     * @return bool Succès de l'opération
     */
    public function delete()
    {
        if (!$this->hydrated){
            $this->hydrate();
        }
        if (!$this->hydrated){
            return false;
        }

        $this->sql = new Querry();
        $this->sql->delete(self::$table);
        $filters = self::uniformizeFilters(['id' => 'id']);
        $this->sql->where($filters);

        $sql = $this->sql->print();

        $stmt = self::$bdd->prepare($sql);
        $success = $stmt->execute(['id' => $this->get('id')]);

        return $success;
    }

    // ----- TOOL METHODS -----

    public static function initBDD()
    {
        if (!self::$bdd) {
            self::$bdd = BDD::getInstance();
        }
    }
}
