<?php

require_once __DIR__ . '/TestSetup.php';

use ExceptionFactory as EF;

class ExceptionFactoryTest extends TestSetup{
    static protected $className = 'ExceptionFactory';
    protected $ExceptionFactory;

    function test__getStack(){
        $EF = new EF();
        $EF->getStack(-2);
        
        $backtrace = $this->ExceptionFactory['properties']['backtrace']->getValue($EF);
        $class = $this->ExceptionFactory['properties']['class']->getValue($EF);
        $function = $this->ExceptionFactory['properties']['function']->getValue($EF);
        $file = $this->ExceptionFactory['properties']['file']->getValue($EF);
        $line = $this->ExceptionFactory['properties']['line']->getValue($EF);
        $arguments = $this->ExceptionFactory['properties']['arguments']->getValue($EF);

        // getStack récupère la backtrace
        $this->assertIsArray($backtrace);

        // getStack récupère la classe
        $this->assertIsString($class);
        $this->assertEquals('ExceptionFactoryTest', $class);

        // getStack récupère la fonction
        $this->assertIsString($function);
        $this->assertEquals('test__getStack', $function);

        // getStack récupère le fichier
        $this->assertIsString($file);

        // getStack récupère la ligne
        $this->assertIsInt($line);

        // getStack récupère les arguments
        $this->assertIsArray($arguments);

        // Il est possible de passer un offset à getStack pour récupérer une ligne précédente
        // Cela est utile lorsqu'une exception est levée dans une méthode appelée par une autre méthode
        $EF->getStack(0);
        $expected = 'PHPUnit\Framework\TestCase';
        $class = $this->ExceptionFactory['properties']['class']->getValue($EF);

        $this->assertEquals($expected, $class);
    }

    function test__setHeader(){
        $EF = new EF();
        
        $this->ExceptionFactory['properties']['message']->setValue($EF, '<message>');
        $this->ExceptionFactory['properties']['class']->setValue($EF, '<class>');
        $this->ExceptionFactory['properties']['function']->setValue($EF, '<function>');
        $this->ExceptionFactory['properties']['file']->setValue($EF, '<file>');
        $this->ExceptionFactory['properties']['line']->setValue($EF, 1);
        $this->ExceptionFactory['properties']['arguments']->setValue($EF, ['<arg1>', '<arg2>']);

        $EF->setHeader();

        // setHeader génère un message avec les informations de la stack
        $message = $this->ExceptionFactory['properties']['message']->getValue($EF);
        $expected = 'In <class>::<function>(<arg1>, <arg2>):' . PHP_EOL;
        $expected .= 'Line 1 in <file>' . PHP_EOL;
        $expected .= '<message>';

        $this->assertEquals($expected, $message);
    }

    function test__generate(){
        $EF = $this->getMockBuilder(EF::class)
            ->onlyMethods(['getStack', 'setHeader'])
            ->getMock();

        $this->ExceptionFactory['properties']['message']->setValue($EF, '<message>');
        $this->ExceptionFactory['properties']['previous']->setValue($EF, new Exception());

        $exceptionClasses = [
            'Exception',
            'LogicException',
            'InvalidArgumentException',
            'RuntimeException'
        ];

        // generate utilise getStack pour récupérer la stack
        $EF->expects($this->exactly(count($exceptionClasses)))
            ->method('getStack');

        // generate utilise setHeader pour générer le message
        $EF->expects($this->exactly(count($exceptionClasses)))
            ->method('setHeader');
        
        foreach($exceptionClasses as $key => $exceptionClass){
            $this->ExceptionFactory['properties']['code']->setValue($EF, $key);
            $exception = $EF->generate($exceptionClass);

            // generate retourne une instance de la classe d'exception passée en paramètre
            $this->assertInstanceOf($exceptionClass, $exception);

            // generate ajoute le message
            $this->assertEquals('<message>', $exception->getMessage());

            // generate ajoute le code
            $this->assertEquals($key, $exception->getCode());

            // generate ajoute le previous
            $this->assertInstanceOf(Exception::class, $exception->getPrevious());
        }
    }

    function test__instance_wrong_parameter(){
        $EF = $this->getMockBuilder(EF::class)
            ->onlyMethods(['generate'])
            ->getMock();

        // injection du mock dans les instances générées par getInstance
        $this->ExceptionFactory['properties']['instance']->setValue($EF, $EF);

        
        $callback = function() use ($EF){
            return $EF;
        };

        // instance_wrong_parameter appelle generate avec LogicException
        $EF->expects($this->once())
            ->method('generate')
            ->with(LogicException::class)
            ->willReturnCallback($callback);

        $parameterName = '<parameterName>';
        $parameterValue = '<parameterValue>';
        $expectedValue = '<expectedValue>';
        $hint = '<hint>';
        $previous = new Exception();
        $backtraceOffset = 1;

        $exception = $EF->instance_wrong_parameter(
            $parameterName,
            $parameterValue,
            $expectedValue,
            $hint,
            $previous,
            $backtraceOffset
        );

        $message = $this->ExceptionFactory['properties']['message']->getValue($exception);
        
        // instance_wrong_parameter utilise $parameterName dans le message
        $this->assertStringContainsString($parameterName, $message);

        // instance_wrong_parameter utilise $parameterValue dans le message
        $this->assertStringContainsString($parameterValue, $message);

        // instance_wrong_parameter utilise $expectedValue dans le message
        $this->assertStringContainsString($expectedValue, $message);

        // instance_wrong_parameter utilise $hint dans le message
        $this->assertStringContainsString($hint, $message);

        // instance_wrong_parameter utilise $previous dans l'exception
        $actual = $this->ExceptionFactory['properties']['previous']->getValue($exception);
        $this->assertEquals($previous, $actual);

        // instance_wrong_parameter utilise $backtraceOffset dans l'exception
        $actual = $this->ExceptionFactory['properties']['backtraceOffset']->getValue($exception);
        $this->assertEquals($backtraceOffset, $actual);
    }

    function test__argument_wrong_type(){
        $EF = $this->getMockBuilder(EF::class)
            ->onlyMethods(['generate'])
            ->getMock();

        // injection du mock dans les instances générées par getInstance
        $this->ExceptionFactory['properties']['instance']->setValue($EF, $EF);

        
        $callback = function() use ($EF){
            return $EF;
        };

        // argument_wrong_type appelle generate avec InvalidArgumentException
        $EF->expects($this->once())
            ->method('generate')
            ->with(InvalidArgumentException::class)
            ->willReturnCallback($callback);
        
        $argumentName = '<argumentName>';
        $argumentValue = '<argumentValue>';
        $expectedTypes = ['<expectedType1>', '<expectedType2>'];
        $hint = '<hint>';
        $previous = new Exception();
        $backtraceOffset = 1;

        $exception = $EF->argument_wrong_type(
            $argumentName,
            $argumentValue,
            $expectedTypes,
            $hint,
            $previous,
            $backtraceOffset
        );

        $message = $this->ExceptionFactory['properties']['message']->getValue($exception);
        
        // argument_wrong_type utilise $argumentName dans le message
        $this->assertStringContainsString($argumentName, $message);

        // argument_wrong_type utilise $argumentValue dans le message
        $this->assertStringContainsString(gettype($argumentValue), $message);

        // argument_wrong_type utilise $expectedTypes dans le message
        foreach($expectedTypes as $expectedType){
            $this->assertStringContainsString($expectedType, $message);
        }

        // argument_wrong_type utilise $hint dans le message
        $this->assertStringContainsString($hint, $message);

        // argument_wrong_type utilise $previous dans l'exception
        $actual = $this->ExceptionFactory['properties']['previous']->getValue($exception);
        $this->assertEquals($previous, $actual);

        // argument_wrong_type utilise $backtraceOffset dans l'exception
        $actual = $this->ExceptionFactory['properties']['backtraceOffset']->getValue($exception);
        $this->assertEquals($backtraceOffset, $actual);
    }

    function test__getInstance(){
        $EF = EF::getInstance();

        // getInstance retourne une instance de ExceptionFactory
        $this->assertInstanceOf(EF::class, $EF);

        // getInstance retourne une instance unique
        $EF2 = EF::getInstance();
        $this->assertEquals($EF, $EF2);
    }

    function test__reset(){
        $EF = EF::getInstance();

        // setup des propriétés
        $this->ExceptionFactory['properties']['message']->setValue($EF, '<message>');
        $this->ExceptionFactory['properties']['code']->setValue($EF, 123);
        $this->ExceptionFactory['properties']['previous']->setValue($EF, new Exception());
        $this->ExceptionFactory['properties']['backtraceOffset']->setValue($EF, 2);

        // setup des propriétés de la stack
        $this->ExceptionFactory['properties']['class']->setValue($EF, '<class>');
        $this->ExceptionFactory['properties']['function']->setValue($EF, '<function>');
        $this->ExceptionFactory['properties']['file']->setValue($EF, '<file>');
        $this->ExceptionFactory['properties']['line']->setValue($EF, 123);
        $this->ExceptionFactory['properties']['arguments']->setValue($EF, ['<arg1>', '<arg2>']);
        $this->ExceptionFactory['properties']['backtrace']->setValue($EF, ['<backtrace>']);

        $EF->reset();

        // reset réinitialise les propriétés
        $this->assertNull($this->ExceptionFactory['properties']['message']->getValue($EF));
        $this->assertNull($this->ExceptionFactory['properties']['code']->getValue($EF));
        $this->assertNull($this->ExceptionFactory['properties']['previous']->getValue($EF));
        $this->assertNull($this->ExceptionFactory['properties']['backtraceOffset']->getValue($EF));

        // reset réinitialise les propriétés de la stack
        $this->assertNull($this->ExceptionFactory['properties']['class']->getValue($EF));
        $this->assertNull($this->ExceptionFactory['properties']['function']->getValue($EF));
        $this->assertNull($this->ExceptionFactory['properties']['file']->getValue($EF));
        $this->assertNull($this->ExceptionFactory['properties']['line']->getValue($EF));
        $this->assertNull($this->ExceptionFactory['properties']['arguments']->getValue($EF));
        $this->assertNull($this->ExceptionFactory['properties']['backtrace']->getValue($EF));
    }

    function test__argument_array_wrong_count(){
        $EF = $this->getMockBuilder(EF::class)
            ->onlyMethods(['generate'])
            ->getMock();

        // injection du mock dans les instances générées par getInstance
        $this->ExceptionFactory['properties']['instance']->setValue($EF, $EF);

        // argument_array_wrong_count appelle generate avec InvalidArgumentException
        $EF->expects($this->once())
            ->method('generate')
            ->with(InvalidArgumentException::class)
            ->willReturnCallback($EF);
        
        $argumentName = '<argumentName>';
        $argumentCount = '<argumentCount>';
        $expectedCount = '<expectedCount>';
        $actualCount = '<actualCount>';
        $hint = '<hint>';
        $previous = new Exception();
        $backtraceOffset = 1;

        $exception = $EF->argument_array_wrong_count(
            $argumentName,
            $argumentCount,
            $expectedCount,
            $actualCount,
            $hint,
            $previous,
            $backtraceOffset
        );

        $message = $this->ExceptionFactory['properties']['message']->getValue($exception);
        
        // argument_array_wrong_count utilise $argumentName dans le message
        $this->assertStringContainsString($argumentName, $message);

        // argument_array_wrong_count utilise $argumentCount dans le message
        $this->assertStringContainsString($argumentCount, $message);

        // argument_array_wrong_count utilise $expectedCount dans le message
        $this->assertStringContainsString($expectedCount, $message);

        // argument_array_wrong_count utilise $actualCount dans le message
        $this->assertStringContainsString($actualCount, $message);

        // argument_array_wrong_count utilise $hint dans le message
        $this->assertStringContainsString($hint, $message);

        // argument_array_wrong_count utilise $previous dans l'exception
        $actual = $this->ExceptionFactory['properties']['previous']->getValue($exception);
        $this->assertEquals($previous, $actual);

        // argument_array_wrong_count utilise $backtraceOffset dans l'exception
        $actual = $this->ExceptionFactory['properties']['backtraceOffset']->getValue($exception);
        $this->assertEquals($backtraceOffset, $actual);
    }

    function test__argument_array_missing_key(){
        $EF = $this->getMockBuilder(EF::class)
            ->onlyMethods(['generate'])
            ->getMock();

        // injection du mock dans les instances générées par getInstance
        $this->ExceptionFactory['properties']['instance']->setValue($EF, $EF);

        
        $callback = function() use ($EF){
            return $EF;
        };

        // argument_array_missing_key appelle generate avec InvalidArgumentException
        $EF->expects($this->once())
            ->method('generate')
            ->with(InvalidArgumentException::class)
            ->willReturnCallback($callback);
        
        $argumentName = '<argumentName>';
        $key = '<key>';
        $hint = '<hint>';
        $previous = new Exception();
        $backtraceOffset = 1;

        $exception = $EF->argument_array_missing_key(
            $argumentName,
            $key,
            $hint,
            $previous,
            $backtraceOffset
        );

        $message = $this->ExceptionFactory['properties']['message']->getValue($exception);
        
        // argument_array_missing_key utilise $argumentName dans le message
        $this->assertStringContainsString($argumentName, $message);

        // argument_array_missing_key utilise $key dans le message
        $this->assertStringContainsString($key, $message);

        // argument_array_missing_key utilise $hint dans le message
        $this->assertStringContainsString($hint, $message);

        // argument_array_missing_key utilise $previous dans l'exception
        $actual = $this->ExceptionFactory['properties']['previous']->getValue($exception);
        $this->assertEquals($previous, $actual);

        // argument_array_missing_key utilise $backtraceOffset dans l'exception
        $actual = $this->ExceptionFactory['properties']['backtraceOffset']->getValue($exception);
        $this->assertEquals($backtraceOffset, $actual);
    }

    function test__file_not_found(){
        $EF = $this->getMockBuilder(EF::class)
            ->onlyMethods(['generate'])
            ->getMock();

        // injection du mock dans les instances générées par getInstance
        $this->ExceptionFactory['properties']['instance']->setValue($EF, $EF);

        
        $callback = function() use ($EF){
            return $EF;
        };

        // file_not_found appelle generate avec Exception
        $EF->expects($this->once())
            ->method('generate')
            ->with(Exception::class)
            ->willReturnCallback($callback);
        
        $file = '<file>';
        $hint = '<hint>';
        $previous = new Exception();
        $backtraceOffset = 1;

        $exception = $EF->file_not_found(
            $file,
            $hint,
            $previous,
            $backtraceOffset
        );

        $message = $this->ExceptionFactory['properties']['message']->getValue($exception);
        
        // file_not_found utilise $file dans le message
        $this->assertStringContainsString($file, $message);

        // file_not_found utilise $hint dans le message
        $this->assertStringContainsString($hint, $message);

        // file_not_found utilise $previous dans l'exception
        $actual = $this->ExceptionFactory['properties']['previous']->getValue($exception);
        $this->assertEquals($previous, $actual);

        // file_not_found utilise $backtraceOffset dans l'exception
        $actual = $this->ExceptionFactory['properties']['backtraceOffset']->getValue($exception);
        $this->assertEquals($backtraceOffset, $actual);
    }
}
