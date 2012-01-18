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