<?php
  require_once("/home/nfs/bs.class.php");
  require_once("/home/nfs/ix.class.php");
  require_once("/home/nfs/ut.class.php");
  require_once '/home/nfs/mw.class.php';
  require_once '/home/nfs/nick.class.php';
  $nname = $uk_nick;

  header("Content-Type:text/html; charset=utf-8"); //application/json

  $ip = new ip(getPost($_GET['callback']));
  $jwt = json_decode(ukDec(getPost($_REQUEST['jwt'])),true);

  $aid = $jwt['aid'];
  $pack = $jwt['pack'];
  $packh = $jwt['pack_h'];
  $ptoken = $jwt['token'];
  $debug = $jwt['debug'];
  $crc = $jwt['crc'];
  $regid = $jwt['regid'];
  $apnsid = $jwt['apnsid'];

  $addr = $_SERVER['HTTP_X_FORWARDED_FOR']; //$_SERVER["REMOTE_ADDR"];
  if ( strlen($pack)<10 || strlen($aid)<10 )  exit;

  require_once("./mlogin.class.php");
  $l = new login($aid,$addr);
  if ($aid == "w20ny-xp15c-04w06-11c54-c10je") {
    $hash = $l->roi($pack,$packh);
    $acc = $l->acc;
    if ($hash == FALSE)  exit;

  } else if ($aid == "n20ts-nx17v-04y04-11a16-b01jl") {
    $hash = $l->ios($pack,$packh);
    if ($hash == FALSE)  exit;

  } else if ($aid == "d20ah-ol17w-03p30-15b05-e05dc") {
    //$jack = ip::depack($pack);
    //if (!is_array($jack))  exit;
    //$wid = $jack->wid;
    //$wpw = $jack->wpw;
    $hash = $l->www($pack,$pack);
    if ($hash == FALSE)  exit;
  } else
    exit;


  $s = $l->state();

  if (strlen($s)>1) {
    if ($crc != "2588546702")
      $ip->j['toast'] = "Not latest version.\nUpdate needed.";
    else
      $ip->j['toast'] = "Welcome to InstaPAY.";

    $ip->j['token'] = tkgen(time());
    $ip->j['salt'] = tksim(tkgen(time()));
    $ip->j['v3'] = 1;
  }


  if ($s == "pure") {
    $salt = $l->j['salt'];
    $m = base64_encode(tkhmac($pack,$salt));
    debug(['STATE',$pack,$packh,$salt,$m]);

    if ($m != $packh) {
      $n = base64_encode(tkhmac($pack,"o20holr15p04o0611z54g10wp"));
      if ($n == $packh) {

        $ip->j['uid'] = tkgen(time());
        $ip->j['nick'] = $nname[rand(0,139)];
        $ip->j['salt'] = "o20holr15p04o0611z54g10wp";
        $ip->j['flag'] = "conquer";

        $mq = new mq();
        $r = array();
        $r['token'] = $ip->j['token'];
        $r['euid'] = $l->j['uid'];
        $r['uid'] = $ip->j['uid'];
        $r['nick'] = $ip->j['nick'];
        $r['aid'] = $aid;
        $r['mode'] = "conquer";
        $r['hash'] = $hash;
        $r['pack'] = $pack;
        $r['pack_h'] = $packh;
        $r['acc'] = $acc;
        $mq->publish("/user_v2", json_encode($r));

        $ip->j['result'] = "new";
        $ip->j['status'] = 1;
        $ip->j['toast'] = "Welcome to InstaPay.";

      } else {

        $ip->j['result'] = "mismatch";
        $ip->j['toast'] = "Access Denied";
        $ip->j['token'] = "";
        $ip->j['salt'] = "";
      }

      $ip->j['status'] = 1;
      
    } else {
      $ip->j['uid'] = $l->j['uid'];
      $ip->j['kfc'] = $l->j['kfc'];
      $ip->j['nick'] = $l->j['nick'];
      $ip->j['email'] = $l->j['email'];
      $ip->j['flag'] = "pure";

      $ip->j['result'] = "ok";
      $ip->j['status'] = 1;
    }

  }

  if ($s == "auth") {
    $ip->j['uid'] = $l->j['uid'];
    $ip->j['kfc'] = $l->j['kfc'];
    $ip->j['nick'] = $l->j['nick'];

    $ip->j['salt'] = "o20holr15p04o0611z54g10wp";
    $ip->j['flag'] = "auth";

    $ip->j['result'] = "auth";
    $ip->j['status'] = 1;
    $ip->j['toast'] = "본인 인증이 되지 않았거나 해제 되었습니다.";
  }

  if ($s == "double") {
    $ip->j['uid'] = $l->j['uid'];
    $ip->j['kfc'] = $l->j['kfc'];
    $ip->j['nick'] = $l->j['nick'];

    $ip->j['salt'] = "o20holr15p04o0611z54g10wp";
    $ip->j['flag'] = "double";

    $ip->j['result'] = "double";
    $ip->j['status'] = 1;
    $ip->j['toast'] = "다른 기기에서 인증이 되어 인증이 해제되었습니다.";
  }

  if ($s == "block") { 
    $ip->j['result'] = "blocked";
    $ip->j['toast'] = "이용 정지 되었습니다. 고객센터에 문의하세요.";
    $ip->j['status'] = 1;
    $ip->j['token'] = "";
    $ip->j['salt'] = "";
  }

  if ($s == "abnormal") {
    $ip->j['result'] = "abnormal";
    $ip->j['status'] = -2;
    $ip->j['toast'] = "Abnormal State. Contack C/S Center.";
    $ip->j['token'] = "";
    $ip->j['salt'] = "";
  }

  if ($s == "newbee") {
    $ip->j['uid'] = tkgen(time());
    $ip->j['nick'] = $nname[rand(0,139)];
    $ip->j['salt'] = "o20holr15p04o0611z54g10wp";
    $ip->j['flag'] = "newbee";

    $mq = new mq();
    $r = array();
    $r['token'] = $ip->j['token'];
    $r['uid'] = $ip->j['uid'];
    $r['nick'] = $ip->j['nick'];
    $r['aid'] = $aid;
    $r['mode'] = "newbee";
    $r['hash'] = $hash;
    $r['pack'] = $pack;
    $r['pack_h'] = $packh;
    $r['acc'] = $acc;
    $mq->publish("/user_v2", json_encode($r));

    $ip->j['result'] = "new";
    $ip->j['status'] = 1;
    $ip->j['toast'] = "Welcome to InstaPay.";
    
  }

  if (tkchk($ip->j['token'])) {
    $mq = new mq();
    $r = array();
    $r['token'] = $ip->j['token'];
    $r['salt'] = $ip->j['salt'];
    $r['flag'] = $ip->j['flag'];
    $r['uid'] = $ip->j['uid'];
    $r['nick'] = $ip->j['nick'];
    $r['aid'] = $aid;
    $r['addr'] = $addr;
    $r['hash'] = $hash;
    $r['pack'] = $pack;
    $r['pack_h'] = $packh;
    $r['regid'] = $regid;
    $r['anpsid'] = $apnsid;
    $r['crc'] = $crc;
    $mq->publish("/token_v2", json_encode($r));
  }

/*
  if (tkchk($uid)) {

    $jsonf = array();

    $ix = new ix();
    $sql = " select bid, charging, trigger from selfie where ddate is null and uid = '".$uid."' ";
    $ar = $ix->query($sql);
    $ro = $ar->num_rows;
    if ($ro==1) {
      $rs = $ar->fetch_array(MYSQLI_NUM);
      $jsonf['bid'] = $rs[0];
      $jsonf['charging'] = $rs[1];
      $jsonf['trigger'] = $rs[2];
    } else {
      $jsonf['bid'] = null;
      $jsonf['charging'] = 0;
      $jsonf['trigger'] = 0;
    }
    if ($ip->j['status'] == 1)
      $ip->j['selfie'] = $jsonf;

  }
*/
?>
