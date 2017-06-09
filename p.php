<?php
require_once '/home/nfs/vendor/autoload.php';
use Endroid\QrCode\QrCode;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\LabelAlignment;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Symfony\Component\HttpFoundation\Response;

require_once("./bs.class.php");
require_once("./ix.class.php");

$gah = GAH();
$ip = new ip($_SERVER, $gah, $_REQUEST, file_get_contents("php://input"), FALSE);
$ip->debug($gah);

$a = json_encode($gah);

$mx = new mx();
$sid = $mx->vtoken($ip->bearer());
if (!uK::tkchk($sid))
  $sid = $ip->req['t'];

if (!uK::tkchk($sid)) {
  $ix = new ix();
  $sql  = " SELECT sid FROM stores ORDER BY RAND() LIMIT 1 ";
  $ar = $ix->query($sql);
  $ro = $ar->num_rows;
  if ($ro!=1)  exit;
  $rs = $ar->fetch_array(MYSQLI_NUM);
  $sid = $rs[0];
}

if (preg_match_all("/Android/",$gah['User-Agent'])>0) {
  header("Location: Intent://payment?q=https://api.instapay.kr/s1/qr?t=".$sid."#Intent;scheme=instapay;package=com.insta.instapay;end");
  exit;
}

if (preg_match_all("/facebook/",$gah['User-Agent'])<1) {
  header("Location: http://www.instapay.kr");
  exit;
}
?>
<html lang="ko">
<head>
<meta charset="utf-8">
<meta property="og:title" content="Park Here.">
<meta property="og:url" content="https://api.instapay.kr/s1/p?s=<?=$sid?>" />
<meta property="og:image" content="https://api.instapay.kr/s1/qr?s=<?=$sid?>" />
<meta property="og:image:width" content="160" />
<meta property="og:image:height" content="160" />
<meta property="og:description" content="Put your hands up." />
<title>Park Here.</title>
</head>
<body>
<img src="https://api.instapay.kr/s1/qr?s=<?=$sid?>" />
</body>
</html>
