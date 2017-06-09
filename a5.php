<?php

$a = array();
// test 22222222222

$a['a']['c'] = 1;
$a['b']['c'] = 9991;
$a['c']['c'] = 444444441;


$c = [ 'a', 'b', 'c' ];
$b = array_reduce($c, function($y, $x) use ($a) {
  $y += $a[$x]['c'];
  return $y;
});

echo $b;


?>
