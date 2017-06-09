<?php





$b = array();
$b['wid'] = "fkjk3jkfj3";
$b['wpw'] = "11111111111fkjk3jkfj3";
$b['sfname'] = "aaaaaaaaaa";

$c = array();
$c['wid'] = "22222fkjk3jkfj3";
$c['wpw'] = "111222222211111111fkjk3jkfj3";
$c['sfname'] = "aaaaaaa222222222222aaa";


$a = array();
$a['staffs'][0] = $b;
$a['staffs'][1] = $c;



foreach ($a['staffs'] as $k) {
  print_r($k);
}

function p1($a) {

}

array_map("p1",$a);

echo urlencode("영동대로");


?>
