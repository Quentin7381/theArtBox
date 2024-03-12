<?php

use ExceptionFactory as EF;

class Condition{
    public $column;
    public $value;
    public $operator;
    public $connector;

    public function __construct($column, $value, $operator = '=', $connector = 'AND'){
        $this->column = $column;
        $this->operator = $operator;
        $this->value = $value;
        $this->connector = $connector;
    }

    public static function generate(...$args){
        // Gestion du cas ou le premier et unique argument est un objet Condition
        if(count($args) === 1 && $args[0] instanceof Condition){
            return $args[0];
        }

        $keys = ['column', 'value', 'operator', 'connector'];

        // Extraction des arguments s'ils sont passes sous forme de tableau
        if(count($args) === 1 && is_array($args[0]) && !isAssoc($args[0])){
            $args = $args[0];
        }

        // Gestion du cas ou les arguments sont passes un par un
        if(!isAssoc($args[0])){
            if(count($args) < 2 || count($args) > 4){
                throw EF::argument_array_wrong_count(
                    'args',
                    count($args),
                    2,
                    4,
                    'The condition() method takes 2 to 4 arguments.'
                );
            }

            $keys = array_slice($keys, 0, count($args));
            $args = array_combine($keys, $args);
        }

        if(
            !isset($args['column']) ||
            !isset($args['value'])
        ) {
            throw EF::argument_array_missing_key('args', 'column or value', null, null, 1);
        }

        return new Condition(
            $args['column'],
            $args['value'],
            $args['operator'] ?? '=',
            $args['connector'] ?? 'AND'
        );
    }
}
