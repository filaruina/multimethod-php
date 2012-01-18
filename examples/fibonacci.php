<?php
require_once('multimethod.php');
$fib = multimethod()
         ->when(0, 0)
         ->when(1, 1)
         ->_default(function($n, $instance) {
            return $instance($n - 1) + $instance($n - 2);
         });
echo $fib(20); //Returns 6765