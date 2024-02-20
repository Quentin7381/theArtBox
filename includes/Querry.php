<?php

/**
 * Représente une requête SQL
 *
 * La classe Querry permet de construire une requête SQL de manière programmatique.
 * Les éléments de la requête sont ajoutés à l'aide de méthodes spécifiques.
 * Pour obtenir la requête SQL finale, il suffit d'appeler la méthode print().
 *
 * ----- SELECT METHODS -----
 * @method select($fields) : Ajoute les colonnes à sélectionner
 * @method from($table) : Ajoute la table à sélectionner
 * @method where($conditions) : Ajoute les conditions de sélection @see Condition
 * @method order_by($orders) : Ajoute les ordres de sélection @see Order
 * @method limit($limit) : Ajoute la limite de sélection
 * @method offset($offset) : Ajoute l'offset de sélection
 * @method group_by($columns) : Ajoute les colonnes de groupement
 * @method having($conditions) : Ajoute les conditions de groupement
 *
 * ----- INSERT METHODS -----
 * @method insert_into($table) : Ajoute la table d'insertion
 * @method values($values) : Ajoute les valeurs à insérer (tableau colonne => valeur)
 *
 * ----- UPDATE METHODS -----
 * @method update($table) : Ajoute la table de mise à jour
 * @method set($sets) : Ajoute les valeurs à mettre à jour (tableau colonne => valeur)
 * @method where($conditions) : Ajoute les conditions de mise à jour @see Condition
 *
 * ----- DELETE METHODS -----
 * @method delete($table) : Ajoute la table de suppression
 * @method where($conditions) : Ajoute les conditions de suppression @see Condition
 */
class Querry {

    protected static $validFields = [
        'operation',
        'table',
        'columns',
        'values',
        'where',
        'order_by',
        'limit',
        'offset',
        'group_by',
        'having',
        'set'
    ];

    protected $querry = [
        'operation' => null,
        'table' => null,
        'columns' => null,
        'values' => null,
        'where' => null,
        'order_by' => null,
        'limit' => null,
        'offset' => null,
        'group_by' => null,
        'having' => null,
        'set' => null
    ];

    public function reset($fields = []){
        if(empty($fields)){
            $fields = self::$validFields;
        }
        
        foreach($fields as $field){
            if(!in_array($field, self::$validFields)){
                throw new Exception('Invalid field');
            }
            $this->querry[$field] = null;
        }
    }

    // ----- INSERT ----- //
    public function insert_into($table){
        $this->reset();
        $this->querry['operation'] = 'INSERT';
        $this->querry['table'] = $table;
    }

    public function values($values, $parametric = true){
        if($this->querry['operation'] != 'INSERT'){
            throw new Exception('Operation must be INSERT');
        }

        if(!isAssoc($values)){
            throw new Exception('Values must be an associative array of column => value');
        }
        
        foreach($values as $column => $value){
            $this->querry['columns'][] = $column;
            if($parametric){
                $this->querry['values'][] = ':' . $value;
            }else{
                $this->querry['values'][] = $value;
            }
        }
    }

    // ----- SELECT ----- //
    public function select($fields){
        $this->reset();
        $this->querry['operation'] = 'SELECT';
        $this->querry['columns'] = $fields;
    }

    public function from($table){
        if($this->querry['operation'] != 'SELECT'){
            throw new Exception('Operation must be SELECT');
        }
        $this->querry['table'] = $table;
    }

    public function where($conditions, $parametric = true){
        if(!in_array($this->querry['operation'], ['SELECT', 'UPDATE', 'DELETE'])){
            throw new Exception('Operation must be SELECT, UPDATE or DELETE');
        }
    
        var_dump($conditions);

        foreach($conditions as $condition){
            if(!($condition instanceof Condition)){
                $condition = Condition::generate($condition);
            }

            $this->querry['where'][] = $condition;
        }
    }

    public function order_by($orders){
        if($this->querry['operation'] != 'SELECT'){
            throw new Exception('Operation must be SELECT');
        }
        
        foreach($orders as $order){
            if(!($order instanceof Order)){
                $order = Order::generate($order);
            }

            $this->querry['order_by'][] = $order;
        }
    }

    public function limit($limit){
        if($this->querry['operation'] != 'SELECT'){
            throw new Exception('Operation must be SELECT');
        }
        $this->querry['limit'] = $limit;
    }

    public function offset($offset){
        if($this->querry['operation'] != 'SELECT'){
            throw new Exception('Operation must be SELECT');
        }
        $this->querry['offset'] = $offset;
    }

    public function group_by($columns){
        if($this->querry['operation'] != 'SELECT'){
            throw new Exception('Operation must be SELECT');
        }
        if(!is_array($columns)){
            $columns = [$columns];
        }
        $this->querry['group_by'] = $columns;
    }

    public function having($conditions){
        if($this->querry['operation'] != 'SELECT'){
            throw new Exception('Operation must be SELECT');
        }
        
        foreach($conditions as $condition){
            if(!($condition instanceof Condition)){
                $condition = Condition::generate($condition);
            }

            $this->querry['having'][] = $condition;
        }
    }

    // ----- UPDATE ----- //

    public function update($table){
        $this->reset();
        $this->querry['operation'] = 'UPDATE';
        $this->querry['table'] = $table;
    }

    public function set($sets, $parametric = true){
        if($this->querry['operation'] != 'UPDATE'){
            throw new Exception('Operation must be UPDATE');
        }
        
        foreach($sets as $set){
            if(!($set instanceof Set)){
                $set = Set::generate($set);
            }

            $this->querry['set'][] = $set;
        }
    }

    // ----- DELETE ----- //

    public function delete($table){
        $this->reset();
        $this->querry['operation'] = 'DELETE';
        $this->querry['table'] = $table;
    }

    // ----- PRINT ----- //

    public function print(){
        $valid_operations = ['SELECT', 'INSERT', 'UPDATE', 'DELETE'];
        if(empty($this->querry['operation']) || !in_array($this->querry['operation'], $valid_operations)){
            throw new Exception('Invalid operation');
        }
        $operation = strtolower($this->querry['operation']);
        $method = 'print_' . $operation;
        return $this->$method();
    }

    protected function print_select(){
        $str = 'SELECT ' . implode(', ', $this->querry['columns']) . ' ';
        $str .= 'FROM ' . $this->querry['table'] . ' ';
        $str .= $this->print_where() . ' ';
        $str .= $this->print_order_by() . ' ';
        $str .= $this->print_limit() . ' ';
        $str .= $this->print_group_by() . ' ';
        $str .= $this->print_having() . ' ';
        return $str;
    }

    protected function print_insert(){
        $str = 'INSERT INTO ' . $this->querry['table'] . ' ';
        $str .= '(' . implode(', ', $this->querry['columns']) . ') ';
        $str .= 'VALUES (' . implode(', ', $this->querry['values']) . ') ';
        return $str;
    }

    protected function print_update(){
        $str = 'UPDATE ' . $this->querry['table'] . ' ';
        $str .= $this->print_set() . ' ';
        $str .= $this->print_where() . ' ';
        return $str;
    }

    protected function print_delete(){
        $str = 'DELETE ';
        $str .= 'FROM ' . $this->querry['table'] . ' ';
        $str .= $this->print_where() . ' ';
        return $str;
    }

    protected function print_set(){
        $str = 'SET ';
        $str .= implode(', ', array_map(function($set){
            return $set->column . ' = ' . $set->value;
        }, $this->querry['set']));
        return $str;
    }

    protected function print_where(){
        if(empty($this->querry['where'])){
            return '';
        }
        $str = 'WHERE ';

        
        foreach($this->querry['where'] as $key => $condition){
            if($key != 0){
                $str .= $condition->connector . ' ';
            }
            $str .= $condition->column . ' ';
            $str .= $condition->operator . ' ';
            $str .= $condition->value . ' ';
        }

        return $str;
    }

    protected function print_order_by(){
        if(empty($this->querry['order_by'])){
            return '';
        }
        $str = 'ORDER BY ';
        $str .= implode(', ', array_map(function($order){
            return $order->field . ' ' . $order->order;
        }, $this->querry['order_by']));
        return $str;
    }

    protected function print_limit(){
        $str = '';
        if(!empty($this->querry['limit'])){
            $str .= 'LIMIT ' . $this->querry['limit'] . ' ';
        }
        $str .= $this->print_offset();

        return $str;
    }

    protected function print_offset(){
        if(empty($this->querry['offset'])){
            return '';
        }
        return 'OFFSET ' . $this->querry['offset'];
    }

    protected function print_group_by(){
        if(empty($this->querry['group_by'])){
            return '';
        }
        return 'GROUP BY ' . implode(', ', $this->querry['group_by']);
    }

    protected function print_having(){
        if(empty($this->querry['having'])){
            return '';
        }
        $str = 'HAVING ';
        
        foreach($this->querry['having'] as $key => $condition){
            if($key != 0){
                $str .= $condition->connector . ' ';
            }
            $str .= $condition->column . ' ';
            $str .= $condition->operator . ' ';
            $str .= $condition->value . ' ';
        }

        return $str;
    }
}

/**
 * Un tableau associatif de
 */
class Set{
    public function __construct($column, $value){
        $this->column = $column;
        $this->value = $value;
    }

    public static function generate($args){
        if(empty($args) || count($args) > 2){
            throw new Exception('Invalid number of arguments');
        }

        if(!isAssoc($args)){
            if(count($args) == 1){
                $args = [
                    'column' => $args[0],
                    'value' => $args[0]
                ];
            } else {
                $args = array_combine(['column', 'value'], $args);
            }
        }

        if(!isset($args['column'])){
            throw new Exception('Missing argument column');
        }

        return new Set($args['column'], $args['value']);
    }
}

class Condition{
    public function __construct($column, $value, $operator = '=', $connector = 'AND'){
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
        $this->connector = $connector;
    }

    public static function generate($args){
        $keys = ['column', 'value', 'operator', 'connector'];

        // Les $args est un tableau non indexé
        if(!isAssoc($args)){
            if(count($args) < 2 || count($args) > 4){
                throw new Exception('Invalid number of arguments');
            }

            $keys = array_slice($keys, 0, count($args));
            $args = array_combine($keys, $args);
        }

        if(
            !isset($args['column']) ||
            !isset($args['value'])
        ) {
            throw new Exception('Missing argument column or value');
        }

        return new Condition(
            $args['column'], 
            $args['value'], 
            $args['operator'] ?? '=',
            $args['connector'] ?? 'AND'
        );
    }
}

class Order{
    public function __construct($field, $order = 'ASC'){
        $this->field = $field;
        $this->order = $order;
    }

    public static function generate($args){
        if(empty($args) || count($args) > 2){
            throw new Exception('Invalid number of arguments');
        }

        if(!isAssoc($args)){
            $keys = array_slice(['field', 'order'], 0, count($args));
            $args = array_combine($keys, $args);
        }

        if(!isset($args['field'])){
            throw new Exception('Missing argument field');
        }

        return new Order($args['field'], $args['order'] ?? 'ASC');
    }
}

function isAssoc($arr){
    return array_values($arr) !== $arr;
}
