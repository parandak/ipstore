<?php
  require_once("./bs.class.php");
  require_once("./ix.class.php");
  header("Content-Type:application/json; charset=utf-8");

  $ip = new ip($_SERVER, GAH(), $_REQUEST, file_get_contents("php://input"));

  $mx = new mx();
  $sid = $mx->vtoken($ip->bearer());
  if (!uK::tkchk($sid))  exit;

  $search = uK::getPost($ip->req['s']);

  $ip->j['status'] = 1;

  $sql  = " select jip, juso from jip where juso like '%".$search."%' order by jip ";
  $ar = $ix->query($sql);
  $ro = $ar->num_rows;

  $jsonf = array();          
  if ($ro>0) {
    for ($i=0;$rs=$ar->fetch_array(MYSQLI_NUM);$i++) {
      $jsonf[$i]["zip"] = $rs[0];
      $jsonf[$i]["juso"] = $rs[1];
    }
  }
        
  $ip->j['juso'] = $jsonf;
  $ip->j["result"] = "ok";          

?>
