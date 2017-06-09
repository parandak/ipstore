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

$dimen = uk::getNumber($ip->req['d']);
if ($dimen != 30)
  $dimen = 165;
$R = uk::getNumber($ip->req['r']);
if ($R<0||$R>255) $R=0;
$G = uK::getNumber($ip->req['g']);
if ($G<0||$G>255) $G=0;
$B = uK::getNumber($ip->req['b']);
if ($B<0||$B>255) $B=0;

//$a = "https://api.instapay.kr/w/i?t=".$sid."&p=".rand(0,10000);
$a = "https://api.instapay.kr/s1/qr?t=".$sid;

$qrCode = new QrCode();
$qrCode
    //->setText('Life is too short to be generating QR codes')
    ->setText($a)

    ->setSize($dimen)
    ->setPadding(10)
    ->setErrorCorrection('low')
    ->setForegroundColor(array('r' => $R, 'g' => $G, 'b' => $B, 'a' => 0))
    //->setBackgroundColor(array('r' => 255, 'g' => 255, 'b' => 255, 'a' => 0))
    //->setLabel('InstaPAY')
    //->setLabelFontSize(16)
    ->setImageType(QrCode::IMAGE_TYPE_PNG)
    //->setLogoPath('./instapay_icon.png')
    //->setLogoSize(50)
;

// now we can directly output the qrcode
header('Content-Type: '.$qrCode->getContentType());
$qrCode->render();

// or create a response object
//$response = new Response($qrCode->get(), 200, array('Content-Type' => $qrCode->getContentType()));

?>
