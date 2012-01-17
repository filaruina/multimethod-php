<?php
require_once 'multimethod.php';

class multimethodTest extends PHPUnit_Framework_TestCase {
    protected $helpers;

    public function setUp() {
        $this->helpers = array(
            'plus1'   => function($n) { return $n + 1; },
            'sum'     => function($a, $b) { return $a + $b; },
            'product' => function($a, $b) { return $a * $b; }
        );
    }

    public function testIdentityDefault() {
        $mm = multimethod();
        $this->assertEquals($mm(1), null);
    }

    public function testIdentityDispatchFunction() {
        $mm = multimethod()->when(1, $this->helpers['plus1']);
        $this->assertEquals($mm(1), 2);
    }

    public function testDefaultDispatchFunction() {
        $mm = multimethod()->_default($this->helpers['plus1']);
        $this->assertEquals($mm(1), 2);
    }

    public function testWhenReturnsPrimitive() {
        $mm = multimethod()->when(1,2);
        $this->assertEquals($mm(1), 2);
        $mm = multimethod()->when(1,true);
        $this->assertEquals($mm(1), true);
        $mm = multimethod()->when(1,"string");
        $this->assertEquals($mm(1), "string");
    }

    public function testWhenChainsAndSelectsCorrectValue() {
        $mm = multimethod()->when(1, "one")
                           ->when(2, "two")
                           ->when(3, "three");
        $this->assertEquals($mm(1), "one");
        $this->assertEquals($mm(2), "two");
        $this->assertEquals($mm(3), "three");
    }

    public function testMultipleArguments() {
        $mm = multimethod(function($a, $b) { return array($a, $b); })
                ->when(array(1,1), $this->helpers['sum'])
                ->when(array(3,3), $this->helpers['product']);
        $this->assertEquals($mm(1,1), 2);
        $this->assertEquals($mm(3,3), 9);
    }

    public function testModifyDispatch() {
        $mm = multimethod()->dispatch($this->helpers['plus1'])
                           ->when(2, $this->helpers['plus1']);
        $this->assertEquals($mm(1), 2);
    }

    public function testOverrideMethod() {
        $mm = multimethod()->when(1, 1)
                           ->when(1, 2);
        $this->assertEquals($mm(1), 2);
    }

    public function testRemoveMethod() {
        $mm = multimethod()->when(1, 1)
                           ->_default(2)
                           ->remove(1);
        $this->assertEquals($mm(1), 2);
    }

    public function testPluckStringDispatchArray() {
        $bornOn = multimethod('type')
                    ->when('person', function($person) { return $person['yearBorn']; })
                    ->when('car', function($car) { return $car['yearBuilt']; });
        $this->assertEquals($bornOn(array('type' => 'person', 'yearBorn' => 1985)), 1985);
        $this->assertEquals($bornOn(array('type' => 'car', 'yearBuilt' => 2011)), 2011);
    }

    public function testPluckStringDispatchObject() {
        $bornOn = multimethod('type')
                    ->when('person', function($person) { return $person->yearBorn; })
                    ->when('car', function($car) { return $car->yearBuilt; });
        $car = new StdClass;
        $car->type = 'car';
        $car->yearBuilt = 2011;
        $person = new StdClass;
        $person->type = 'person';
        $person->yearBorn = 1985;
        
        $this->assertEquals($bornOn($person), 1985);
        $this->assertEquals($bornOn($car), 2011);
    }

    public function testPluckUndefinedProperty() {
        $mm = multimethod('type')->_default(1);
        $obj = new StdClass();
        $this->assertEquals($mm($obj), 1);
    }

    public function testDeepEquality() {
        $greatPairs = multimethod()
                        ->when(array('Salt', 'Pepper'), 'Shakers')
                        ->when(array(array('name' => 'Bonnie'), array('name' => 'Clyde')), 'Robbers')
                        ->_default("?");
        $this->assertEquals('?', $greatPairs(array('MJ', 'Pippen')));
        $this->assertEquals('Shakers', $greatPairs(array('Salt', 'Pepper')));
        $this->assertEquals('Robbers', $greatPairs(array(array('name' => 'Bonnie'), array('name' => 'Clyde'))));
    }
}