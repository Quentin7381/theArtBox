<?php

require_once __DIR__.'/../config/Config.php';
require_once __DIR__.'/../includes/Autoload.php';

$cfg->override('db_name', 'theartbox_test');

class TestSetup extends PHPUnit\Framework\TestCase{
    static protected $className = 'none';

    function setUp():void{
        $this->setReflexionClassUp();

        // Mock for BDD
        $this->BDD = $this->createMock(BDD::class);
    }

    function setReflexionClassUp(){
        $className = static::$className;
        $this->$className = [
            'class' => null,
            'properties' => [],
            'methods' => [],
            'instance' => null
        ];

        $this->$className['class'] = new ReflectionClass($className);

        // set private/protected properties accessible
        foreach($this->$className['class']->getProperties() as $property){
            $property->setAccessible(true);
            $this->$className['properties'][$property->getName()] = $property;
        }

        // set private/protected methods accessible
        foreach($this->$className['class']->getMethods() as $method){
            $method->setAccessible(true);
            $this->$className['methods'][$method->getName()] = $method;
        }

        // create an instance of the class
        $this->$className['instance'] = $this->$className['class']->newInstanceWithoutConstructor();
    }

    function test__phpunitWorks(){
        $this->assertTrue(true);
    }

}
