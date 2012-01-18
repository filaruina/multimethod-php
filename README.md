# multimethod.php

Multimethod.php is a port from [Kris Jordan multimethod.js](https://github.com/KrisJordan/multimethod-js) that is inspired by Clojure multimethods.

The following description about what multimethods are was taken from his project (and most of this readme and examples were based on his own).

Multimethods are a functional programming control structure that allow you
to dynamically build-up and manipulate the dispatching behavior of a 
polymorphic function.

# Main differences between multimethod.js and multimethod.php

- PHP has 'default' as a reserved word. So, to define a default method you have to use _default
- Every function receives the instance used as a last paramenter. That is necessary since you can't reuse the object inside an anonymous function so any recursive function wouldn't work without it. Look for the fibonacci example to better understand this.
- Objects and Arrays are really different in PHP. I though it would be better to let the string plucking work with objects and arrays, instead of just objects. (this doesn't make sense for JS)

# API

- Constructor: `multimethod`( [fn | string] ):  No arg constructor uses an
  identity function for `dispatch`. Single arg constructor is a shortcut for
  calling `dispatch` with the same argument.
- `dispatch`(fn | string): Sets the `multimethod`'s `dispatch` function. String
  values are transformed into a pluck function which projects a single
  property value (or key if using an array) from the first argurment.
- `when`(match, fn | value): Add a `method` to be invoked when the `dispatch`
  return value matches 'match'. If a non-function `value` is provided it will
  be returned directly. Calling `when` with the same `match` value twice will 
  override the previously registered `method`.
- `remove`(match): Remove a `method` by it's `match` value.
- `default`(fn | value): Catch-all case when no `method` match is found.

# Examples

Some examples to show how multimethod works. They may be found in examples folder.

    <?php
    require_once('multimethod.php');
    $area = multimethod()
              ->dispatch(function($o) {
                  return $o->shape;
              })
              ->when("square", function($o) {
                  return pow($o->side, 2);
              });
    $square = new StdClass();
    $square->shape = "square";
    $square->side = 2;
    echo $area($square) . "\n"; //Returns 4

    $circle = new StdClass();
    $circle->shape = "circle";
    $circle->radius = 5;
    echo $area($circle) . "\n"; //Returns null

    $area->_default(function($o) {
        return 'Unknown shape: ' . $o->shape;
    });
    echo $area($circle) . "\n"; //Returns Unknown shape: circle

    $area->when('circle', function($o) {
        return pi() * pow($o->radius, 2);
    });
    echo $area($circle) . "\n"; //Returns 78.539816339745
    echo $area($square) . "\n"; //Returns 4 (Notice it is the same value)

    $area->remove('circle');
    echo $area($circle) . "\n"; //Returns Unknown shape: circle (again)

Some things should be noted on this example:

- Methods are registered using when
- Dispatch defines the value to match with when
- you may overwrite, remove and add new methods at runtime
- Fallback is defined by _default which is run when no method is found

Now let's see a recursive fibonacci example:

    <?php
    require_once('multimethod.php');
    $fib = multimethod()
             ->when(0, 0)
             ->when(1, 1)
             ->_default(function($n, $instance) {
                return $instance($n - 1) + $instance($n - 2);
             });
    echo $fib(20); //Returns 6765

Note that the instance is provided as a second parameter for the _default function (also happens whith methods)

One las example:

    <?php
    require_once('multimethod.php');
    $hitpoints = multimethod()
                   ->dispatch(function($player) {
                       return $player->powerUp;
                   })
                   ->when(array('type' => 'star'), 'Infinity')
                   ->_default(5);
    $starPower = array('type' => 'star');
    $mario = new StdClass();
    $mario->powerUp = $starPower;
    echo $hitpoints($mario) . "\n"; // Returns Infinity

    $mario->powerUp = null;
    echo $hitpoints($mario) . "\n"; // Returns 5

    $godModeCheat = function() use ($starPower) { return $starPower; };
    $hitpoints->dispatch($godModeCheat);
    echo $hitpoints($mario) . "\n"; // Returns Infinity you cheater!

Note that:

- You may use arrays for the matching of the methods
- We may override dispatch changing the whole behavior of the multimethod

# about the port

Basically the process of making the port consisted of copying the unit tests and implementing the code based on Kris Jordan multimethod-js.
Most of the features are currently replicated but more tests are needed.

# TODO

- undefined property (Test exists)