<?php
  require_once("./bs.class.php");
  require_once("./ix.class.php");
  header("Content-Type:application/json; charset=utf-8");

  $ip = new ip($_SERVER, GAH(), $_REQUEST, file_get_contents("php://input"));

  $mx = new mx();
  $sid = $mx->vtoken($ip->bearer());
  if (!uK::tkchk($sid))  exit;

  if ( $ip->req['op'] == "deposit" && uK::tkchk($ip->req['tid']) ) {

    $ix = new ix();
    $sql = " UPDATE tras SET tstatus = 'canceled' WHERE tid = '".$tid."' ";
    $ar = $ix->query($sql);
    $ro = $ix->affected_rows;
    if ($ro == 1) {
      $ip->j['result'] = "ok";
      $ip->j['status'] = 1;
    }
        
  }
    

?>
