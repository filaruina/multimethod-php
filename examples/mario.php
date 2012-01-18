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