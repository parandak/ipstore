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

  preg_match("/^(\W+)(\d*)$/", $search, $m); // 1: title, 2: number.
  if (count($m)) {
    $title = $m[1];
    $number = $m[2];
  } else
    $title = $search;
  $title = str_replace(" ","",$title);


  $road = "";
  $dong = "";
  preg_match("/^(.*)[로|길]$/", $title, $m); // 1: title, 2: number.
  if (count($m)) 
    $road = $m[1];
  else {
    preg_match("/^(.*)[동|리]$/", $title, $m); // 1: title, 2: number.
    if (count($m)) 
      $dong = $m[1];
  }

  $jsonf = array();          
  $ix = new ix();
  if (strlen($dong)<1) {
    // default search : considered as being absent with suffix ( ro or gil ).
    $sql  = " select distinct d1, d2, d4, d6, d9, d12, d16, d18, d19, d20 from zip1007 ";
    $sql .= " where match( d9 ) against('".$title."*' IN BOOLEAN MODE) ";
    if ($number>0)
      $sql .= " and d12 = ".$number." ";
    $sql .= " order by d1 ";
    $sql .= " limit 100 ";
    $ar = $ix->query($sql);
    $ro = $ar->num_rows;

    if ($ro>0) {
      for ($i=0;$rs=$ar->fetch_array(MYSQLI_NUM);$i++) {
        $d1 = $rs[0];
        $d9 = $rs[4];
        preg_match("/^(.*)로$/", $d9, $m);
        if (count($m)>1) {
          $jsonf[$i]["zip"] = $d1;
          $a = $rs[1]." ".$rs[2]." ".$rs[3]." ".$rs[4]." ".$rs[5]." ".$rs[6];
          $jsonf[$i]["juso"] = str_replace("  "," ",$a);
          //$jsonf[$i]["nzip"] = $d1;
        } else {
          $jsonf[$i]["zip"] = $rs[0];
          $a = $rs[1]." ".$rs[2]." ".$rs[3]." ".$rs[4]." ".$rs[5];
          $jsonf[$i]["juso"] = str_replace("  "," ",$a);
          //$jsonf[$i]["nzip"] = $rs[0];
        }
      }
    }
  }

  if (strlen($road)<1) {
    // with dong or ree. 
    $sql  = " select distinct d1, d2, d4, d6, d9, d12, d16, d18, d19, d20, d22 from zip1007 ";
    $sql .= " where match( d18, d19, d20 ) against('".$title."*' IN BOOLEAN MODE) ";
    if ($number>0)
      $sql .= " and d22 = ".$number." ";
    $sql .= " order by d1 ";
    $sql .= " limit 100 ";
    $ar = $ix->query($sql);
    $ro = $ar->num_rows;

    if ($ro>0) {
      for ($i=0;$rs=$ar->fetch_assoc();$i++) {
        $jsonf[$i]["zip"] = $rs['d1'];
        if (strlen($rs['d6'])>0)
          if (strlen($rs['d20'])>0 && $rs['d6']!=$rs['d20'])
            $c = $rs['d6']." (".$rs['d20'].") ".$rs['d19']." ".$rs['d22'];
          else
            $c = $rs['d6']." ".$rs['d19']." ".$rs['d22'];
        else
          if (strlen($rs['d20'])>0 && $rs['d18']!=$rs['d20'])
            $c = $rs['d18']." (".$rs['d20'].")"." ".$rs['d22'];
          else
            $c = $rs['d18']." ".$rs['d22'];
        $a = $rs['d2']." ".$rs['d4']." ".$c;
        $b = $rs['d9']." ".$rs['d12']." ".$rs['d16'];
        $b1 = trim(str_replace("  "," ",$b));
        $jsonf[$i]["juso"] = str_replace("  "," ",$a)." (".$b1.")";
        //$jsonf[$i]["nzip"] = $rs['d1'];
      }
    }
  }
  $ip->j['juso'] = $jsonf;
  $ip->j["result"] = "ok";          

?>
