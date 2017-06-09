<?php
  require_once("./bs.class.php");
  require_once("./ix.class.php");
  header("Content-Type:application/json; charset=utf-8");

  $ip = new ip($_SERVER, GAH(), $_REQUEST, file_get_contents("php://input"));

  $mx = new mx();
  $sid = $mx->vtoken($ip->bearer());
  if (!uK::tkchk($sid))  exit;

  $tid = uk::getPost($ip->req['tid']);
  $q = uK::getPost($ip->req['q']);
  $p = uK::getNumber($ip->req['offset']);

  if ($p<1)  $p=0;
  $qb = uK::getPost($ip->req['qb']);
  $qe = uK::getPost($ip->req['qe']);
  $t = strtolower(uK::getPost($ip->req['t']));
  if ($t != "giro")
    $t = "";
    
  $limit = uK::getNumber($ip->req['limit']);  
  //if ($limit<10)  $limit = 10;

  $ix = new ix();

  $sql  = " select count(t.tid) as count, SUM(t.tsum) as sum, t.status  ";
  $sql .= " from ( select t.tid, t.tsum, t.status ";
  $sql .= " from tras t, means m, goods g, juso j, users u  ";
  $sql .= "   where t.mid = m.mid and t.gid = g.gid and t.jid = j.jid ";
  $sql .= "   and t.uid = u.uid ) t ";
  $sql .= " GROUP BY t.status ";
  $ar = $ix->query($sql);
  //$rs = $ar->fetch_array(MYSQLI_NUM); 
  //$rs = $ar->fetch_assoc();


  $a = array();

  foreach ($ar as $i) {
    if ( $i['status'] == "complete" ) {
      $a["complete"]["count"] = intval($i['count']);
      $a["complete"]["paymentAmount"] = intval($i['sum']);
    }

    if ( $i['status'] == "ing" ) {
      $a["notpaid"]["count"] = intval($i['count']);
      $a["notpaid"]["paymentAmount"] = intval($i['sum']);
    }

    if ( $i['status'] == "canceled" ) {
      $a["canceled"]["count"] = intval($i['count']);
      $a["canceled"]["paymentAmount"] = intval($i['sum']);
    }
  }

  $r = [ 'complete', 'notpaid', 'canceled' ];
  $a['total']['count'] = array_reduce($r, function($y, $x) use ($a) {
    $y += $a[$x]['count'];
    return $y;
  });
  $a['total']['paymentAmount'] = array_reduce($r, function($y, $x) use ($a) {
    $y += $a[$x]['paymentAmount'];
    return $y;
  });

  $ip->j["summary"] = $a;
  $ip->j['count'] = $a['total']['count'];

  if (uK::tkchk($tid)) {
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
  $sql .= "      , g.mw_glist, j.zip, CONCAT(ifnull(j.fixed,'월하의'),' ',ifnull(j.user,'공동묘지')), g.gid, t.mw_res ";
  $sql .= "      , t.tid, u.nick, t.trnum, date_format(t.adjdate, '%Y-%m-%d %H:%i:%s'), u.auth ";
  $sql .= "      , g.renum ";
  $sql .= "   from tras t left join means m on t.mid = m.mid left join goods g on t.gid = g.gid left join juso j on t.jid = j.jid ";
  $sql .= "   left join users u on t.uid = u.uid ";
  $sql .= " where m.mtype is not null AND ( t.status = 'complete' OR t.status = 'ing' OR t.status = 'canceled' ) ";
  //$sql .= " where t.uid = '".$uid."' AND t.status = 'complete' ";
  //debug($sql);
  if (strlen($q)>1)
    $sql .= " AND t.adate >= '".$q."-01' AND t.adate <= '".$q."-31' ";
  else {
    if (strlen($qb)>1)
      $sql .= " AND t.adate <= '".$qb."' ";
    if (strlen($qe)>1)
      $sql .= " AND t.adate >= '".$qe."' ";
  }
  if (strlen($adate)>2)
    $sql .= " AND t.adate < '".$adate."' ";

  if ($t == "giro")
    $sql .= " AND LENGH(g.mw_glist)>2 ";

  $sql .= " order by t.adate desc ";
  if ($limit>0)
    $sql .= " limit ".$p.",".$limit." ";
  $ip->debug($sql);
  $ar = $ix->query($sql);

  $jsonf = array();          
  //for ($i=0;$rs=$ar->fetch_assoc();$i++)
  //  $jsonf[$i] = $rs;
  $ro = $ar->num_rows;

  if ($ro>0) {
    for ($i=0;$rs=$ar->fetch_array(MYSQLI_NUM);) {
      $gname = $rs[0];
      if ($gname != null) {
        $a = json_decode($rs[19],TRUE);
        $jsonf[$i]["uname"] = is_array($a) ? $a['uname'] : '무명';
        //$jsonf[$i]["payerBirthdayTimestamp"] = isset($a['birth']) ? $a['birth'] : 'none';
        if (strlen($a['birth'])==8) {
          $jsonf[$i]["payerBirthdayTimestamp"] = substr($a['birth'],0,4)."-".substr($a['birth'],4,2)."-".substr($a['birth'],6,2);
        }
      
        $jsonf[$i]["applicationNo"] = (is_null($rs[20]) ? uK::renum() : $rs[20]);

        $jsonf[$i]["buyerName"] = $rs[16];
        $jsonf[$i]["approvalNo"] = $rs[17];
        $jsonf[$i]["calculatedTimestamp"] = $rs[18];
        $jsonf[$i]["canceledTimestamp"] = null;
        $jsonf[$i]["payerName"] = $rs[16];
        $jsonf[$i]["productName"] = $rs[0];
        //$jsonf[$i]["adate"] = timeago(strtotime($rs[1]));
        $jsonf[$i]["adate"] = $rs[1];
        $jsonf[$i]["paymentAmount"] = $rs[2];
        $jsonf[$i]["bankName"] = $rs[3];                            
        $jsonf[$i]["cardName"] = null;                            
        $jsonf[$i]["mname"] = $rs[3]."-".$rs[6];                            
        $jsonf[$i]["paymentMethods"] = $rs[4];
        $jsonf[$i]["tstatus"] = $rs[5];
        $jsonf[$i]["sname"] = $rs[7];
        $jsonf[$i]['orderNo'] = uK::renum();
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
        $jsonf[$i]["payerAddress"] = $rs[12];
        //$jsonf[$i]["gid"] = $rs[13];
        $jsonf[$i]["tid"] = $rs[15];
        $jsonf[$i]["installment"] = 1;



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
