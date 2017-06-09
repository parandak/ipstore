<?php
  class px {
    var $db = null;
    function open() { $this->db = @mysql_connect("10.0.75.233","yuki","1112") or die("Arrk."); @mysql_select_db("insta", $this->db); @mysql_query("set names utf8"); }
    function close() { @mysql_close($this->db); }
    function execute($sql) { $ar = @mysql_query($sql); return $ar; }
    function query($sql) { $ar = @mysql_query($sql); return $ar; }
    function fetch($ar) { $rs = @mysql_fetch_array($ar); return $rs; }
    function row($ar) { $co = @mysql_num_rows($ar); return $co; }
    function affect() { $ro = @mysql_affected_rows(); return $ro; }
    function transaction() { @mysql_query("start transaction"); }
    function commit() { @mysql_query("commit"); }
    function rollback() { @mysql_query("rollback"); }
    function names($charset) { @mysql_query("set names ".$charset); }
  }

  class ix extends mysqli {
    var $ho = "";
    var $id = "";
    var $pa = "";
    var $db = "";
 
    public function __construct($a = "10.0.75.233", $b = "yuki", $c = "1112", $d = "insta") {
      $this->ho = $a;
      $this->id = $b;
      $this->pa = $c;
      $this->db = $d;
      parent::__construct($a, $b, $c, $d);
      parent::set_charset("utf8");
    }

    public function reconnect() {
      parent::__construct($this->ho, $this->id, $this->pa, $this->db);
      parent::set_charset("utf8");
    }

    public function __destruct() {
      $this->close();
    }

    //(connect_errno) $connect_error;
    //autocommit(FALSE);
    //affected_rows;
    //MYSQLI_ASSOC

  }

?>
