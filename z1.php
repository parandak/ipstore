<?php

error_reporting(E_ERROR | E_WARNING | E_PARSE);
ini_set('display_errors','On');
ini_set('error_log','logs/error.log');

require_once '/home/nfs/vendor/autoload.php';
/*
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;
use PhpAmqpLib\Exception\AMQPTimeoutException;
use GuzzleHttp\Client;
use \Firebase\JWT\JWT;
*/

  class mx 
  {
    var $co = null;
    var $db = null;

    public function __construct() {
      $this->co = new MongoClient("mongodb://10.0.75.143");
      //$a = new MongoDB\Client("mongodb://10.0.75.143");
      $this->db = $this->co->insta;
      //parent::__construct($this->db,"insta");
    }

    public function __destruct() {
      $this->co->close();
    }

    public function vtoken($token) {
      if (strlen($token)<2)  return FALSE;

      $d = date("YmdHis",time());
      $q = array("token"=>$token,"adate"=>$d);
      $f = $this->db->sla->find($q)->count();
      if ($f>2)  return FALSE;

      $a = array();
      $a['adate'] = date("YmdHis",time());
      $a['token'] = $token;
      $this->db->sla->insert($a);

      $q = array("token"=>$token);
      $f = $this->db->stores->find($q)->sort(array('_id'=>-1))->limit(1)->getNext();
      return $f['sid'];
    }


  }



  $mx = new mx();

  $a = $mx->vtoken("k20ws-tu17d-05q26-15s27-o59vf");

  print_r($a);



?>
a
