<?php
header('Access-Control-Allow-Origin: *');
//header('Access-Control-Allow-Credentials: true');
//header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS,HEAD');
//header('Access-Control-Allow-Headers: Accept, Accept-Encoding, Accept-Language, Access-Control-Allow-Headers, Access-Control-Request-Headers, Access-Control-Request-Method, Access-Control-Request-Methods, Authorization, Connection, Content-Type, Host, Origin, Referer, User-Agent, X-Requested-With');
header('Access-Control-Allow-Headers: Authorization');


$a = array();
$a['a1'] = "a1a1a1a1a1a1";
$a['b1'] = "b1b1b1b1b1b1";

$b = json_encode($a);
echo $b;

?>
