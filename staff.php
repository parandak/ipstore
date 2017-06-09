<?php
  require_once("./bs.class.php");
  require_once("./ix.class.php");
  header("Content-Type:application/json; charset=utf-8");

  $ip = new ip($_SERVER, GAH(), $_REQUEST, file_get_contents("php://input"));

  $mx = new mx();
  $sid = $mx->vtoken($ip->bearer());
  if (!uK::tkchk($sid))  exit;

  class staff {

    public $sid;
    public $ix;
    public $mx;
    

    function __construct($sid) {
      $this->sid = $sid;
      $this->ix = new ix();
      $this->mx = new mx();
    }

      /*
      foreach ($j['staffs'] as $a) {
        $a['sid'] = $sid;
        try {
          $d = $this->mx->stores->insert($a);
        } catch (Exception $e) {
          //if ($e->getCode() == 11000) {
          //}
        }
      }
      */

    function erase($wid) {
      if (strlen($wid)<2)  return "improper";

      $sql = " SELECT json FROM stores WHERE sid = '".$this->sid."' ";
      $ar = $this->ix->query($sql);
      $ro = $ar->num_rows;
      if ($ro==1)
        $rs = $ar->fetch_assoc();

      $a = json_decode($rs['json'],TRUE);
      if (!is_array($a))  return "ERR";

      $b = $a['staffs'];
      //foreach ($a['staffs'] as $b) {
      //  try {
      //  } catch (Exception $e) { }
      for ($i=0;$i<count($b);$i++) {
        if ($b[$i]['wid'] == $wid) {
          unset($b[$i]);
          break;
        }
      }
      $a['staffs'] = $b;
      $sql = " UPDATE stores SET json = '".json_encode($a,JSON_UNESCAPED_UNICODE)."' WHERE sid = '".$this->sid."' ";
      $ar = $this->ix->query($sql);
      $ro = $this->ix->affected_rows;

      $q = array("wid"=>$wid, "sid"=>$this->sid);
      $d = $this->mx->db->stores->remove($q);

      return "ok";

    }

    function append($j) {
      if (!is_array($j))  return "improper";

      $sql = " SELECT json FROM stores WHERE sid = '".$this->sid."' ";
      $ar = $this->ix->query($sql);
      $ro = $ar->num_rows;
      if ($ro==1)
        $rs = $ar->fetch_assoc();
      
      $a = json_decode($rs['json'],TRUE);
      if (!is_array($a))  return "ERR";

      try {
        $j['sid'] = $this->sid;
        $d = $this->mx->db->stores->insert($j);

        $a['staffs'][count($a['staffs'])] = $j;
        $sql = " UPDATE stores SET json = '".json_encode($a,JSON_UNESCAPED_UNICODE)."' WHERE sid = '".$this->sid."' ";
        $ar = $this->ix->query($sql);
        $ro = $this->ix->affected_rows;

      } catch (Exception $e) {
        if ($e->getCode() == 11000)
          return "dup";
        else
          return $e->getCode();
      }
      
      return "ok";

    }

  } 

  $ip->j['status'] = 1;
  $ip->j['result'] = "ok";

  $m = $ip->method;
  $s = new staff($sid);
  switch($m) {
    case "DELETE":
      $a = $s->erase($ip->req['wid']);
      if ($a != "ok")
        $ip->j['result'] = $a;
        
      break;

    case "POST":
      $a = $s->append($ip->jwt());
      if ($a != "ok")
        $ip->j['result'] = $a;
      break;

    default:
      $ip->j['result'] = "method";
      break; 
  }

    

?>
