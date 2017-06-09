<?php
  require_once("/home/nfs/bs.class.php");
  require_once("/home/nfs/ix.class.php");
  require_once("/home/nfs/ut.class.php");
  require_once("/home/nfs/mw.class.php");
  header("Content-Type:text/html; charset=utf-8");

  $ip = new ip(getPost($_GET['callback']));

  $token = getPost($_REQUEST['token']);
  $tid = getPost($_REQUEST['offset']);
  $q = getPost($_REQUEST['q']);
  $qb = getPost($_REQUEST['qb']);
  $qe = getPost($_REQUEST['qe']);
  $t = strtolower(getPost($_REQUEST['t']));
  if ($t != "giro")
    $t = "";
    
  $limit = getNumber($_REQUEST['limit']);  
  //if ($limit<10)  $limit = 10;

  if (!tkchk($token))  exit;

  $mx = new mx();
  $uid = $mx->vtoken($token);
  if (!tkchk($uid))  exit;

  $ix = new ix();

  if (tkchk($tid)) {
    $sql  = " SELECT date_format(adate, '%Y-%m-%d %H:%i:%s') FROM tras WHERE tid = '".$tid."' ";
    $ar = $ix->query($sql);
    $ro = $ar->num_rows;
    if ($ro>0) {
      $rs = $ar->fetch_array(MYSQLI_NUM);
      $adate = $rs[0];
    }
  }

  $sql  = " select g.gname, date_format(t.adate, '%Y-%m-%d %H:%i:%s') as adate, t.tsum, m.mname, m.mtype as means ";
  $sql .= "      , t.status as tstatus, m.mnum, g.sname, LEFT(g.qr,100), g.mw_res ";
  $sql .= "      , g.mw_glist, j.zip, CONCAT(ifnull(j.fixed,'월하의'),' ',ifnull(j.user,'공동묘지')), g.gid, t.mw_res, t.tid ";
  $sql .= " from tras t left join means m on t.mid = m.mid left join goods g on t.gid = g.gid left join juso j on t.jid = j.jid ";
  $sql .= " where t.uid = '".$uid."' AND t.status = 'complete' ";
  debug($sql);
  if (strlen($q)>1)
    $sql .= " AND t.adate >= '".$q."-01' AND t.adate <= '".$q."-31' ";
  else {
    if (strlen($qb)>1)
      $sql .= " AND t.adate <= '".$qb."' ";
    if (strlen($qe)>1)
      $sql .= " AND t.adate >= '".$qe."' ";
  }
  if ($t == "giro")
    $sql .= " AND LENGH(g.mw_glist)>2 ";
  
  if (strlen($adate)>2)
    $sql .= " AND t.adate < '".$adate."' ";
  $sql .= " order by t.adate desc ";
  if ($limit>0)
    $sql .= " limit ".$limit." ";
  //debug($sql);
  $ar = $ix->query($sql);

  $jsonf = array();          
  //for ($i=0;$rs=$ar->fetch_assoc();$i++)
  //  $jsonf[$i] = $rs;
  $ro = $ar->num_rows;
  if ($ro>0) {
    for ($i=0;$rs=$ar->fetch_array(MYSQLI_NUM);) {
      $gname = $rs[0];
      if ($gname != null) {
        $jsonf[$i]["gname"] = $rs[0];
        //$jsonf[$i]["adate"] = timeago(strtotime($rs[1]));
        $jsonf[$i]["adate"] = $rs[1];
        $jsonf[$i]["tsum"] = $rs[2];
        $jsonf[$i]["mname"] = $rs[3]."-".$rs[6];                            
        $jsonf[$i]["means"] = $rs[4];
        $jsonf[$i]["tstatus"] = $rs[5];
        $jsonf[$i]["sname"] = $rs[7];
        //$jsonf[$i]["m_ord_id"] = randnum(6);
        $g_mw_res = json_decode($rs[9],true);
        if (is_array($g_mw_res))
          $jsonf[$i]["m_ord_id"] = $g_mw_res['m_ord_id'];

        $gtype = "online";
        $qr = $rs[8];
        if (strlen($qr) == 3) {
          $gtype = "home";
          $t_mw_res = json_decode($rs[14],true);
          if (is_array($g_mw_res))
            $jsonf[$i]["m_ord_id"] = $t_mw_res['pay_trans_id'];
        } else if (strlen($qr) > 100)
          $gtype = "giro";
        else {
          parse_str($qr,$b);
          if (isset($b['t'])) 
            $gtype = "donate";
        }
        

        $mw_glist = json_decode($rs[10],true);
        if (is_array($mw_glist)) {
          $gtype = "giro";
          $jsonf[$i]["payer"] = $mw_glist['client_name'];
        }
        
        $jsonf[$i]["gtype"] = $gtype;


        $jsonf[$i]["zip"] = $rs[11];
        $jsonf[$i]["juso"] = $rs[12];
        $jsonf[$i]["gid"] = $rs[13];
        $jsonf[$i]["tid"] = $rs[15];


        $i++;
      }
    }
  }
  $ip->j['tras'] = $jsonf;
  if (count($jsonf)<1)
    $ip->j["toast"] = "데이터가 없습니다.";
  $ip->j["result"] = "ok";
  $ip->j['status'] = 1;

?>
