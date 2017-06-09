<?php
  require_once("./bs.class.php");
  require_once("./ix.class.php");
  header("Content-Type:application/json; charset=utf-8"); 

  $ip = new ip($_SERVER, GAH(), $_REQUEST, file_get_contents("php://input"));

  $pack = uk::getPost($ip->req['pack']);
  if ($ip->method != "GET" || $ip->req['aid'] != "d20ah-ol17w-03p30-15b05-e05dc" || strlen($pack)<2 )  exit;

  $mx = new mx();
  $q = array("hash"=>$pack);
  $f = $mx->db->stores->findOne($q);
  
  $ip->debug($f);
  
  if (uK::tkchk($f['sid'])) {
    $token = uK::tkgen(time());
    $u = $mx->db->stores->update(
      array('hash'=>$pack),
      array('$set'=>array('token'=>$token)),
      array('upsert'=>false, 'multiple'=>false)
    );

    if ($u['updatedExisting'] == 1) {
      $ip->j['result'] = "ok";
      $ip->j['status'] = 1;
      $ip->j['toast'] = "Welcome to InstaPay.";
      $ip->j['token'] = $token;
    }
  }

?>
