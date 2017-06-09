<?php

$a = "{\"sname\":\"장나라 팬클럽\",\"info\":{\"biz\":\"11111111\",\"tel\":\"120312312\"},\"juso\":{\"zip\":\"06181\",\"fixed\":\"서울특별시 강남구 테헤란로 520 삼안빌딩\",\"user\":\"12층\"},\"adjust\":{\"bank\":\"국민은행\",\"account\":\"123456-78-901234\"},\"staffs\":[{\"wid\":\"email@exmaple.com\",\"hash\":\"d111af8fdbebc04a825c66c2cbf549c65803f38ff7b44ff05bf38ed3aab095b6\",\"sfname\":\"김광현\",\"dep\":\"후원회\",\"tel\":\"010-2345-6789\"},{\"sfname\":\"김광현\",\"dep\":\"후원회\",\"wid\":\"email1@exmaple.com\",\"hash\":\"842d5e9295913abad6aef395a\",\"tel\":\"010-2345-6789\",\"sid\":\"h20rk-un17m-05e17-15x26-c53lp\",\"_id\":{\"$id\":\"591c127fdde601ff30dac045\"}}]}";


$b = json_decode($a,TRUE);


print_r($b['staffs'][1]);
unset($b['staffs'][1]);
print_r($b);

$c = json_encode($b,JSON_UNESCAPED_UNICODE);
echo $c;

?>
