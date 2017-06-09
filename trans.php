<?php
  require_once("/home/nfs/bs.class.php");
  require_once("/home/nfs/ix.class.php");
  require_once("/home/nfs/ut.class.php");
  require_once("/home/nfs/mw.class.php");
  header("Content-Type:text/html; charset=utf-8");

use \Firebase\JWT\JWT;

  //debug(getallheaders());

$key = "example_key";
$token = array(
    "iss" => "http://example.org",
    "aud" => "http://example.com",
    "iat" => 1356999524,
    "nbf" => 1357000000
);

$key = "m20ku-cy17b-05i17-17e28-b01pt";
$a = array(
  "sfname" => "김광현",
  "dep" => "후원회",
  "wid" => "email1@exmaple.com",
  "hash" => "842d5e9295913abad6aef395a",
  "tel" => "010-2345-6789"
); 

$jwt = JWT::encode($a, $key);

echo $jwt.PHP_EOL;



try {
$a = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzZm5hbWUiOiJcdWFlNDBcdWFkMTFcdWQ2MDQiLCJkZXAiOiJcdWQ2YzRcdWM2ZDBcdWQ2OGMiLCJ3aWQiOiJlbWFpbEBleG1hcGxlLmNvbSIsImhhc2giOiI4NDJkNWU5Mjk1OTEzYWJhZDZhZWYzOTVhIiwidGVsIjoiMDEwLTIzNDUtNjc4OSJ9.PYspLQqEeg0tDo_TWbUym6NjTw2CYC8GoY5PiJkPxvI";
$decoded = (array) JWT::decode($a, $key, array('HS256'));
} catch (Exception $e) {
  echo $e->getCode().PHP_EOL;
  echo $e->getMessage();
}

print_r($decoded);

/*
JWT::$leeway = 60; // $leeway in seconds
$decoded = JWT::decode($jwt, $key, array('HS256'));
print_r($decoded);
*/



?>

