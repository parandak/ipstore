<?php
  require_once("./bs.class.php");
  require_once("./ix.class.php");
  header("Content-Type:application/json; charset=utf-8");

  $ip = new ip($_SERVER, GAH(), $_REQUEST, file_get_contents("php://input"));

  $mx = new mx();
  $sid = $mx->vtoken($ip->bearer());
  if (!uK::tkchk($sid))  exit;

  class store {

    public $sid;
    public $ix;
    public $mx;
    

    function __construct($sid) {
      $this->sid = $sid;
      $this->ix = new ix();
      $this->mx = new mx();
    }

    function retrive() {
      $rs = NULL;

      $sql = " SELECT * FROM stores WHERE sid = '".$this->sid."' ";
      $ar = $this->ix->query($sql);
      $ro = $ar->num_rows;
      if ($ro==1)
        $rs = $ar->fetch_assoc();

      return $rs;
    }

    function update($j) {

      if (!is_array($j))  return "improper";
/*
      if (!is_array($j['info']))  return "info";
      if (!is_array($j['juso']))  return "juso";
      if (!is_array($j['adjust']))  return "adjust";
      if (!is_array($j['staffs']))  return "staffs";
*/
      $sql = " SELECT json FROM stores WHERE sid = '".$this->sid."' ";
      $ar = $this->ix->query($sql);
      $ro = $ar->num_rows;
      if ($ro==1)
        $rs = $ar->fetch_assoc();
      
      $a = json_decode($rs['json'],TRUE);
      if (!is_array($a))  return "ERR";
      if (is_array($j['info']))
        $a['info'] = $j['info'];
      if (is_array($j['juso']))
        $a['juso'] = $j['juso'];
      if (is_array($j['adjust']))
        $a['adjust'] = $j['adjust'];
      if (is_array($j['staffs'])) 
        $a['staffs'] = $j['staffs'];
      
      $sql = " UPDATE stores SET json = '".json_encode($a,JSON_UNESCAPED_UNICODE)."' WHERE sid = '".$this->sid."' ";
      $ar = $this->ix->query($sql);
      $ro = $this->ix->affected_rows;

      return "ok";
    }

    function create($j) {
      if (!is_array($j))  return "improper";
      if (!is_array($j['info']))  return "info";
      if (!is_array($j['juso']))  return "juso";
      if (!is_array($j['adjust']))  return "adjust";
      if (!is_array($j['staffs']))  return "staffs";

      $sid = uK::tkgen(time());

      foreach ($j['staffs'] as $a) {
        $a['sid'] = $sid;
        try {
          $d = $this->mx->stores->insert($a);
        } catch (Exception $e) {
          if ($e->getCode() == 11000) {
            return "dup";
          }
        }
      }

      $sql  = " INSERT INTO stores SET ";
      $sql .= "   adate = now() ";
      $sql .= " , sid = '".$sid."' ";
      $sql .= " , sname = '".$j['sname']."' ";
      $sql .= " , stype = 'donate' ";
      $sql .= " , json = '".json_encode($j,JSON_UNESCAPED_UNICODE)."' ";
      $ar = $this->ix->query($sql);
      $ro = $this->ix->affected_rows;

      return "ok";
    }

  } 

  $ip->j['status'] = 1;
  $ip->j['result'] = "ok";

  $m = $ip->method;
  $s = new store($sid);
  switch($m) {
    case "GET":
      $rs = $s->retrive(); 
      $p = array();

      $j = json_decode($rs['json'],TRUE);
      $p['sid'] = $s->sid;
      $p['adate'] = $rs['adate'];
      $p['representativeName'] = "aaaaaaa";
      $p['sname'] = $rs['sname'];
      $p['info'] = $j['info'];
      $p['juso'] = $j['juso']; 
      $p['adjust'] = $j['adjust']; 
      $p['staffs'] = $j['staffs']; 

      $ip->j['store'] = $p;

      break;

    case "PUT":
      //$ip->debug($ip->jwt());
      $a = $s->update($ip->jwt());
      if ($a != "ok")
        $ip->j['result'] = $a;
        
      break;

    case "POST":
      $ip->debug($ip->jwt());
      $a = $s->create($ip->jwt());
      if ($a != "ok")
        $ip->j['result'] = $a;
      break;

    default:
      $ip->j['result'] = "method";
      break; 
  }

    

?>
