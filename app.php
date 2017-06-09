<?php
  require_once("./bs.class.php");
  require_once("./ix.class.php");
  //header("Content-Type:text/html; charset=utf-8");

$ip = new ip($_SERVER, GAH(), $_REQUEST, file_get_contents("php://input"), FALSE);

$mx = new mx();
$sid = $mx->vtoken($ip->bearer());
if (!uK::tkchk($sid))  
  $sid = $ip->req['s'];

if (!uK::tkchk($sid)) {
  $ix = new ix();
  $sql  = " SELECT sid FROM stores ORDER BY RAND() LIMIT 1 ";
  $ar = $ix->query($sql);
  $ro = $ar->num_rows;
  if ($ro!=1)  exit;
  $rs = $ar->fetch_array(MYSQLI_NUM);
  $sid = $rs[0];
}

header('Location: Intent://payment?q=https://api.instapay.kr/s1/qr?t='.$sid.'#Intent;scheme=instapay;package=com.insta.instapay;end');

?>
