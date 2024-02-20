<?php

require_once __DIR__.'/TestSetup.php';

class QuerryTest extends TestSetup{
    static protected $className = 'Querry';
    protected $Querry;

    public function test__reset(){
        // Reset réinitialise les valeurs de la requête
        $querry = new Querry();
        $value = [
            'operation' => 'SELECT',
            'table' => 'table',
            'columns' => ['column'],
            'values' => ['value'],
            'where' => ['where'],
            'order_by' => ['order_by'],
            'limit' => ['limit'],
            'group_by' => ['group_by'],
            'having' => ['having'],
            'set' => ['set']
        ];

        $this->Querry['properties']['querry']->setValue($querry, $value);

        // Avant l'appel à reset, les valeurs sont définies
        foreach($value as $key => $val){
            $this->assertNotNull(
                $this->Querry['properties']['querry']->getValue($querry)[$key]
            );
        }

        $querry->reset();

        // Après l'appel à reset, les valeurs sont réinitialisées
        foreach($value as $key => $val){
            $this->assertNull(
                $this->Querry['properties']['querry']->getValue($querry)[$key]
            );
        }

        // Sur un appel avec un tableau de valeurs, seules les valeurs définies sont réinitialisées
        $this->Querry['properties']['querry']->setValue($querry, $value);

        $querry->reset(['operation', 'table']);

        foreach($value as $key => $val){
            if(in_array($key, ['operation', 'table'])){
                $this->assertNull(
                    $this->Querry['properties']['querry']->getValue($querry)[$key]
                );
            }
            else {
                $this->assertNotNull(
                    $this->Querry['properties']['querry']->getValue($querry)[$key]
                );
            }
        }

        // Si les valeurs à réinitialiser ne sont pas valides, une exception est levée
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid field');

        $querry->reset(['invalid']);
    }

    public function test__insert_into(){
        // insert_into fait appel a reset() pour réinitialiser les valeurs de la requête
        $mock = $this->getMockBuilder('Querry')
            ->setMethods(['reset'])
            ->getMock();

        $mock->expects($this->once())
            ->method('reset');


        // insert_into définit la table dans laquelle insérer des données
        $table = 'table';

        $mock->insert_into($table);

        $this->assertEquals(
            $table,
            $this->Querry['properties']['querry']->getValue($mock)['table']
        );

        // insert_into définit l'opération à INSERT
    }

    public function test__values(){
        // values ne peut être appelé que si l'opération est INSERT
        $wrongOperations = ['SELECT', 'UPDATE', 'DELETE', null];
        foreach($wrongOperations as $operation){
            $querry = new Querry();
            $querry->operation = $operation;

            try{
                $querry->values(['value']);
            }
            catch(Exception $e){
                $this->assertEquals('Operation must be INSERT', $e->getMessage());
            }
        }

        // si $values n'est pas un tableau associatif, une exception est levée
        $querry = new Querry();
        $values = ['value_1', 'value_2'];
        $this->Querry['properties']['querry']->setValue($querry, ['operation' => 'INSERT']);

        try{
            $querry->values($values);
        }
        catch(Exception $e){
            $this->assertEquals('Values must be an associative array of column => value', $e->getMessage());
        }

        // values ajoute les valeurs à la requête
        $querry = new Querry();
        $this->Querry['properties']['querry']->setValue($querry, ['operation' => 'INSERT']);
        $values = [
            'column_1' => 'value_1',
            'column_2' => 'value_2'
        ];

        $querry->values($values, false);

        $this->assertEquals(
            $values,
            $this->Querry['properties']['querry']->getValue($querry)['values']
        );

        // si $parametric est vrai ou non défini, les valeurs sont préfixées par ':'
        $querry = new Querry();
        $values = [
            'column_1' => 'value_1',
            'column_2' => 'value_2'
        ];

        $querry->values($values);

        $this->assertEquals(
            [
                'column_1' => ':value_1',
                'column_2' => ':value_2'
            ],
            $this->Querry['properties']['querry']->getValue($querry)['values']
        );

        print_r($this->Querry['properties']['querry']->getValue($querry)['values']);

        $querry->values($values, true);

        $this->assertEquals(
            [
                'column_1' => ':column_1',
                'column_2' => ':column_2'
            ],
            $this->Querry['properties']['querry']->getValue($querry)['values']
        );
    }

    public function test__select(){
        // select fait appel a reset() pour réinitialiser les valeurs de la requête
        $mock = $this->getMockBuilder('Querry')
            ->setMethods(['reset'])
            ->getMock();

        $mock->expects($this->once())
            ->method('reset');

        // select définit les colonnes à sélectionner
        $columns = ['column_1', 'column_2'];

        $mock->select($columns);

        $this->assertEquals(
            $columns,
            $this->Querry['properties']['querry']->getValue($mock)['columns']
        );

        // select définit l'opération à SELECT
        $this->assertEquals(
            'SELECT',
            $this->Querry['properties']['querry']->getValue($mock)['operation']
        );
    }

    public function test__from(){
        // L'opération FROM ne peut être définie que si l'opération est SELECT
        $wrongOperations = ['INSERT', 'UPDATE', 'DELETE', null];
        foreach($wrongOperations as $operation){
            $querry = new Querry();
            $querry->operation = $operation;

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Operation must be SELECT');

            $querry->from('table');
        }

        // from définit la table à partir de laquelle sélectionner des données
        $querry = new Querry();
        $table = 'table';

        $querry->from($table);

        $this->assertEquals(
            $table,
            $this->Querry['properties']['querry']->getValue($querry)['table']
        );
    }

    public function test__where(){
        // where ne peut être appelé que si l'opération est SELECT, UPDATE ou DELETE
        $wrongOperations = ['INSERT', null];
        foreach($wrongOperations as $operation){
            $querry = new Querry();
            $querry->operation = $operation;

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Operation must be SELECT, UPDATE or DELETE');

            $querry->where('where');
        }

        // where ajoute une condition à la requête
        $querry = new Querry();
        $where = [new Condition('column', 'value', '=', 'AND')];

        $querry->where($where);

        $this->assertEquals(
            [$where],
            $this->Querry['properties']['querry']->getValue($querry)['where']
        );

        // si l'un des arguments de where n'est pas une Condition, une Condition est créée via Condition::generate

        $mock = $this->getMockBuilder('Condition')
            ->setMethods(['generate'])
            ->getMock();

        $mock->expects($this->once())
            ->method('generate')
            ->with('column', 'value', '=', 'AND')
            ->willReturn(new Condition('column', 'value', '=', 'AND'));

        $querry = new Querry();
        $where = ['column', 'value', '=', 'AND'];

        $querry->where($where);

        $this->assertEquals(
            [new Condition('column', 'value', '=', 'AND')],
            $this->Querry['properties']['querry']->getValue($querry)['where']
        );
    }

    public function test__order_by(){
        // order_by ne peut être appelé que si l'opération est SELECT
        $wrongOperations = ['INSERT', 'UPDATE', 'DELETE', null];
        foreach($wrongOperations as $operation){
            $querry = new Querry();
            $querry->operation = $operation;

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Operation must be SELECT');

            $querry->order_by('order_by');
        }

        // order_by ajoute une condition de tri à la requête
        $querry = new Querry();
        $order_by = new Order('column', 'ASC');

        $querry->order_by($order_by);

        $this->assertEquals(
            [$order_by],
            $this->Querry['properties']['querry']->getValue($querry)['order_by']
        );

        // si l'un des arguments de order_by n'est pas un Order, un Order est créé via Order::generate

        $mock = $this->getMockBuilder('Order')
            ->setMethods(['generate'])
            ->getMock();

        $mock->expects($this->once())
            ->method('generate')
            ->with('column', 'ASC')
            ->willReturn(new Order('column', 'ASC'));

        $querry = new Querry();

        $querry->order_by('column', 'ASC');

        $this->assertEquals(
            [new Order('column', 'ASC')],
            $this->Querry['properties']['querry']->getValue($querry)['order_by']
        );
    }

    public function test__limit(){
        // limit ne peut être appelé que si l'opération est SELECT
        $wrongOperations = ['INSERT', 'UPDATE', 'DELETE', null];
        foreach($wrongOperations as $operation){
            $querry = new Querry();
            $querry->operation = $operation;

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Operation must be SELECT');

            $querry->limit('limit');
        }

        // limit ajoute une limite à la requête
        $querry = new Querry();
        $limit = 'limit';

        $querry->limit($limit);

        $this->assertEquals(
            $limit,
            $this->Querry['properties']['querry']->getValue($querry)['limit']
        );
    }

    public function test__group_by(){
        // group_by ne peut être appelé que si l'opération est SELECT
        $wrongOperations = ['INSERT', 'UPDATE', 'DELETE', null];
        foreach($wrongOperations as $operation){
            $querry = new Querry();
            $querry->operation = $operation;

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Operation must be SELECT');

            $querry->group_by('group_by');
        }

        // group_by ajoute une condition de regroupement à la requête
        $querry = new Querry();
        $group_by = ['group_by'];

        $querry->group_by($group_by);

        $this->assertEquals(
            $group_by,
            $this->Querry['properties']['querry']->getValue($querry)['group_by']
        );

        // si $columns n'est pas un tableau, il est transformé en tableau
        $querry = new Querry();
        $group_by = 'group_by';

        $querry->group_by($group_by);

        $this->assertEquals(
            [$group_by],
            $this->Querry['properties']['querry']->getValue($querry)['group_by']
        );
    }

    public function test__having(){
        // having ne peut être appelé que si l'opération est SELECT
        $wrongOperations = ['INSERT', 'UPDATE', 'DELETE', null];
        foreach($wrongOperations as $operation){
            $querry = new Querry();
            $querry->operation = $operation;

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Operation must be SELECT');

            $querry->having('having');
        }

        // having ajoute une condition à la requête
        $querry = new Querry();
        $having = [new Condition('column', 'value', '=', 'AND')];

        $querry->having($having);

        $this->assertEquals(
            [$having],
            $this->Querry['properties']['querry']->getValue($querry)['having']
        );

        // si l'un des arguments de having n'est pas une Condition, une Condition est créée via Condition::generate

        $mock = $this->getMockBuilder('Condition')
            ->setMethods(['generate'])
            ->getMock();

        $mock->expects($this->once())
            ->method('generate')
            ->with('column', 'value', '=', 'AND')
            ->willReturn(new Condition('column', 'value', '=', 'AND'));

        $querry = new Querry();
        $having = ['column', 'value', '=', 'AND'];

        $querry->having($having);

        $this->assertEquals(
            [new Condition('column', 'value', '=', 'AND')],
            $this->Querry['properties']['querry']->getValue($querry)['having']
        );
    }

    public function test__update(){
        // update utilise reset() pour réinitialiser les valeurs de la requête
        $mock = $this->getMockBuilder('Querry')
            ->setMethods(['reset'])
            ->getMock();

        $mock->expects($this->once())
            ->method('reset');
        
        // update définit la table à mettre à jour
        $table = 'table';

        $mock->update($table);

        $this->assertEquals(
            $table,
            $this->Querry['properties']['querry']->getValue($mock)['table']
        );

        // update définit l'opération à UPDATE
        $this->assertEquals(
            'UPDATE',
            $this->Querry['properties']['querry']->getValue($mock)['operation']
        );
    }

    public function test__set(){
        // set ne peut être appelé que si l'opération est UPDATE
        $wrongOperations = ['SELECT', 'INSERT', 'DELETE', null];
        foreach($wrongOperations as $operation){
            $querry = new Querry();
            $querry->operation = $operation;

            $this->expectException(Exception::class);
            $this->expectExceptionMessage('Operation must be UPDATE');

            $querry->set('set');
        }

        // set ajoute des valeurs à mettre à jour à la requête
        $querry = new Querry();
        $set = ['column' => 'value'];

        $querry->set($set, false);

        $this->assertEquals(
            $set,
            $this->Querry['properties']['querry']->getValue($querry)['set']
        );

        // si $parametric est vrai ou non défini, les valeurs sont préfixées par ':'
        $querry = new Querry();
        $set = ['column' => 'value'];

        $querry->set($set);

        $this->assertEquals(
            [
                'column' => ':column'
            ],
            $this->Querry['properties']['querry']->getValue($querry)['set']
        );

        $querry->set($set, true);

        $this->assertEquals(
            [
                'column' => ':column'
            ],
            $this->Querry['properties']['querry']->getValue($querry)['set']
        );
    }

    public function test__delete(){
        // delete utilise reset() pour réinitialiser les valeurs de la requête
        $mock = $this->getMockBuilder('Querry')
            ->setMethods(['reset'])
            ->getMock();

        $mock->expects($this->once())
            ->method('reset');

        // delete définit la table à supprimer
        $table = 'table';
        $mock->delete($table);
        
        $this->assertEquals(
            $table,
            $this->Querry['properties']['querry']->getValue($mock)['table']
        );

        // delete définit l'opération à DELETE
        $this->assertEquals(
            'DELETE',
            $this->Querry['properties']['querry']->getValue($mock)['operation']
        );
    }

    // ----- PRINT METHODS ----- //

    public function test__print(){
        // print fait appel à l'opération pour choisir la méthode de print à appeler
        $mock = $this->getMockBuilder('Querry')
            ->setMethods(['print_select', 'print_insert', 'print_update', 'print_delete'])
            ->getMock();

        // select
        $mock->expects($this->once())
            ->method('print_select');

        $this->Querry['properties']['querry']->setValue($mock, ['operation' => 'SELECT']);

        @$mock->print();

        // insert
        $mock->expects($this->once())
            ->method('print_insert');

        $this->Querry['properties']['querry']->setValue($mock, ['operation' => 'INSERT']);

        @$mock->print();

        // update
        $mock->expects($this->once())
            ->method('print_update');

        $this->Querry['properties']['querry']->setValue($mock, ['operation' => 'UPDATE']);

        @$mock->print();

        // delete
        $mock->expects($this->once())
            ->method('print_delete');

        $this->Querry['properties']['querry']->setValue($mock, ['operation' => 'DELETE']);

        @$mock->print();

        // si l'opération n'est pas valide, une exception est levée
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Invalid operation');

        $this->Querry['properties']['querry']->setValue($mock, ['operation' => 'invalid']);

        $mock->print();

        // si l'opération n'est pas définie, une exception est levée
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('No operation defined');

        $this->Querry['properties']['querry']->setValue($mock, []);

        $mock->print();
    }

    public function test__print_select(){
        // print_select fait appel à print_(where, order_by, limit, group_by, having) pour générer la requête
        $mock = $this->getMockBuilder('Querry')
            ->setMethods(['print_where', 'print_order_by', 'print_limit', 'print_group_by', 'print_having'])
            ->getMock();
        
        $mock->expects($this->once())
            ->method('print_where')
            ->willReturn('WHERE <conditions>');

        $mock->expects($this->once())
            ->method('print_order_by')
            ->willReturn('ORDER BY <order>');

        $mock->expects($this->once())
            ->method('print_limit')
            ->willReturn('LIMIT <limit>');

        $mock->expects($this->once())
            ->method('print_group_by')
            ->willReturn('GROUP BY <group>');

        $mock->expects($this->once())   
            ->method('print_having')
            ->willReturn('HAVING <having>');

        // initialisation des valeurs de la requête
        $this->Querry['properties']['querry']->setValue($mock, [
            'operation' => 'SELECT',
            'columns' => ['column'],
            'table' => 'table',
            'where' => ['where'],
            'order_by' => ['order_by'],
            'limit' => ['limit'],
            'group_by' => ['group_by'],
            'having' => ['having']
        ]);

        // print_select génère la requête de sélection attendue
        $expected =
            'SELECT column ' .
            'FROM table ' .
            'WHERE <conditions> ' .
            'ORDER BY <order> ' .
            'LIMIT <limit> ' .
            'GROUP BY <group> ' .
            'HAVING <having> ';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_select']->invoke($mock)
        );
    }

    public function test__print_insert(){
        $querry = new Querry();

        // initialisation des valeurs de la requête
        $this->Querry['properties']['querry']->setValue($querry, [
            'operation' => 'INSERT',
            'table' => 'table',
            'columns' => ['column_1', 'column_2'],
            'values' => ['value_1', 'value_2']
        ]);

        // print_insert génère la requête d'insertion attendue
        $expected =
            'INSERT INTO table (column_1, column_2) ' .
            'VALUES (value_1, value_2) ';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_insert']->invoke($querry)
        );
    }

    public function test__print_update(){
        // print_update fait appel à print_(set, where) pour générer la requête
        $mock = $this->getMockBuilder('Querry')
            ->setMethods(['print_set', 'print_where'])
            ->getMock();
        
        $mock->expects($this->once())
            ->method('print_set')
            ->willReturn('SET <set>');

        $mock->expects($this->once())
            ->method('print_where')
            ->willReturn('WHERE <conditions>');

        // initialisation des valeurs de la requête
        $this->Querry['properties']['querry']->setValue($mock, [
            'operation' => 'UPDATE',
            'table' => 'table',
            'set' => ['set'],
            'where' => ['where']
        ]);

        // print_update génère la requête de mise à jour attendue
        $expected =
            'UPDATE table ' .
            'SET <set> ' .
            'WHERE <conditions> ';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_update']->invoke($mock)
        );
    }

    public function test__print_delete(){
        // print_delete fait appel à print_where pour générer la requête
        $mock = $this->getMockBuilder('Querry')
            ->setMethods(['print_where'])
            ->getMock();
        
        $mock->expects($this->once())
            ->method('print_where')
            ->willReturn('WHERE <conditions>');

        // initialisation des valeurs de la requête
        $this->Querry['properties']['querry']->setValue($mock, [
            'operation' => 'DELETE',
            'table' => 'table',
            'where' => ['where']
        ]);

        // print_delete génère la requête de suppression attendue
        $expected =
            'DELETE FROM table ' .
            'WHERE <conditions> ';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_delete']->invoke($mock)
        );
    }

    public function test__print_where(){
        // print_where génère la condition WHERE de la requête
        $querry = new Querry();
        $where = [
            new Condition('column_1', 'value_1', '=', 'AND'),
            new Condition('column_2', 'value_2', '=', 'OR')
        ];

        $this->Querry['properties']['querry']->setValue($querry, ['where' => $where]);

        $expected = 'WHERE column_1 = value_1 OR column_2 = value_2 ';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_where']->invoke($querry)
        );
    }

    public function test__print_order_by(){
        // print_order_by génère la condition ORDER BY de la requête
        $querry = new Querry();
        $order_by = [
            new Order('column_1', 'ASC'),
            new Order('column_2', 'DESC')
        ];

        $this->Querry['properties']['querry']->setValue($querry, ['order_by' => $order_by]);

        $expected = 'ORDER BY column_1 ASC, column_2 DESC';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_order_by']->invoke($querry)
        );
    }

    public function test__print_limit(){
        // print_limit utilise print_offset pour générer la condition OFFSET de la requête
        $mock = $this->getMockBuilder('Querry')
            ->setMethods(['print_offset'])
            ->getMock();

        $mock->expects($this->once())
            ->method('print_offset')
            ->willReturn('OFFSET <offset>');

        // print_limit génère la condition LIMIT de la requête
        $limit = 10;

        $this->Querry['properties']['querry']->setValue($mock, ['limit' => $limit]);

        $expected = 'LIMIT 10 OFFSET <offset>';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_limit']->invoke($mock)
        );
    }

    public function test__print_offset(){
        // print_offset génère la condition OFFSET de la requête
        $querry = new Querry();
        $offset = 10;

        $this->Querry['properties']['querry']->setValue($querry, ['offset' => $offset]);

        $expected = 'OFFSET 10';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_offset']->invoke($querry)
        );
    }

    public function test__print_group_by(){
        // print_group_by génère la condition GROUP BY de la requête
        $querry = new Querry();
        $group_by = ['column'];

        $this->Querry['properties']['querry']->setValue($querry, ['group_by' => $group_by]);

        $expected = 'GROUP BY column';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_group_by']->invoke($querry)
        );
    }

    public function test__print_having(){
        // print_having génère la condition HAVING de la requête
        $querry = new Querry();
        $having = [
            new Condition('column_1', 'value_1', '=', 'AND'),
            new Condition('column_2', 'value_2', '=', 'OR')
        ];

        $this->Querry['properties']['querry']->setValue($querry, ['having' => $having]);

        $expected = 'HAVING column_1 = value_1 OR column_2 = value_2 ';

        $this->assertEquals(
            $expected,
            $this->Querry['methods']['print_having']->invoke($querry)
        );
    }
}
