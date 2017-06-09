<?php
  require_once("./bs.class.php");
  require_once("./ix.class.php");
  header("Content-Type:application/json; charset=utf-8"); 


$ix = new ix();
$sql = 'UPDATE insta.stores SET json = \'{"sname":"홍길동 팬클럽","info":{"biz":"11111111","tel":"120312312"},"juso":{"zip":"06181","fixed":"서울특별시 강남구 테헤란로 520 삼안빌딩","user":"12층"},"adjust":{"bank":"국민은행","account":"123456-78-901234"},"staffs":[{"wid":"master@instapay.kr","hash":"09d5db61e3502dbcc198db44bd6ffa8fb105a4151b8b6e3f575fb64d0662e35b","sfname":"김광현","dep":"지원팀","tel":"010-2345-6789"}]}\' WHERE sid = \'h20rk-un17m-05e17-15x26-c53lp\'';

//$ix->query($sql);

$a = array();
$a['wid'] = "admin@instapay.kr";
$a['wpw'] = "1234";

$b = json_encode($a);

//echo hash("sha256",$b);

$mx = new mx();


echo uK::tkgen(time());

$j = array();

$j['wid'] = "admin@instapay.kr";
$j['hash'] = "34ec870ed32781e760cf9018b2bb88da07e40fbaa0b39e2dac72310ae0cdef79";
$j["sfname"] = "김경수";
$j["dep"] = "대표이사";
$j["tel"] = "010-4949-5437";
$j["level"] = "admin";
$j["sid"] = "z20nn-ss17c-05u30-16w52-u40rm";

//$d = $mx->db->stores->insert($j);

print_r($d);


?>
