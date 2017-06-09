<?php
  //error_reporting(E_ALL);
  error_reporting(E_ERROR | E_WARNING | E_PARSE);
  //ini_set('display_errors','On');
  ini_set('error_log','logs/error.log');

  require_once '/home/nfs/vendor/autoload.php';
  use PhpAmqpLib\Connection\AMQPStreamConnection;
  use PhpAmqpLib\Message\AMQPMessage;
  use PhpAmqpLib\Wire\AMQPTable;
  use PhpAmqpLib\Exception\AMQPTimeoutException;
  use GuzzleHttp\Client;
  use \Firebase\JWT\JWT;

  if (!isset($uk_noheader)) {
    header("Expires: 0");
    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Allow-Methods: GET,POST,PUT,DELETE,OPTIONS,HEAD');
    header('Access-Control-Allow-Headers: Accept, Accept-Encoding, Accept-Language, Access-Control-Allow-Headers, Access-Control-Request-Headers, Access-Control-Request-Method, Access-Control-Request-Methods, Authorization, Connection, Content-Type, Host, Origin, Referer, User-Agent, X-Requested-With');
    header_remove('x-powered-by');
  }

  function GAH() {
    if (function_exists('getallheaders')) {
      return getallheaders();
    } else 
      return NULL;
  }

  class InstaPay
  {
    private static $instance = null;

    public static function getInstance() {
      if ( !self::$instance ) self::$instance = new self();
      return self::$instance;
    }
  
    private function __construct() {
    }
  }

  class InstaPayException extends Exception
  {
    public function __construct($message, $code = 0, Exception $prev = null) {
      $json = json_decode($message);
      if (is_object($json)) {
        parent::__construct($json->msg, $json->status);
      } else {
        parent::__construct($message, $code, $prev);
      }
    }

    public function __toString() {
      return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
  }

  class ip
  {
    var $conn = null;
    var $chan = null;

    public $j;
    public $de;
    private $cb;
    private $t;

    public $s;

    public $method;
    public $addr;
    public $body;
    public $header;

    public $bearer;

    public function __construct($s, $h, $r, $b, $t=TRUE) {
      $this->conn = new AMQPStreamConnection('10.0.75.136', 5672, 'yuki', '1112', '/');
      $this->chan = $this->conn->channel();

      $this->j = array();
      $this->j["status"] = -1;
      $this->j["result"] = "denial";

      $this->cb = $r['callback'];
      $this->t = $t;
      $this->s = $s;
      $this->de = TRUE;

      $this->header = $h;
      $this->req = $r;
      $this->body = $b;

      $this->addr = $s['HTTP_X_FORWARDED_FOR'];
      $this->method = $s['REQUEST_METHOD'];

    }

    public function jwt() {
      $t = null;

      if ( strlen($this->body)>2 && strlen($this->bearer)>2 ) {
        try {
          $r = JWT::decode($this->body, $this->bearer, array('HS256'));
          $s = json_encode($r,JSON_UNESCAPED_UNICODE);
          $t = json_decode($s,TRUE);
        } catch (Exception $e) { 
          $this->debug($e->getMessage());
        }
      }
      return $t;
    }

    public function __destruct() {

      //print_r($this->json);
      if ($this->t) {
        if ($this->de)  $this->debug($this->j);
        $result = json_encode($this->j,JSON_UNESCAPED_UNICODE);
        $callback = $this->cb;
        if (strlen($callback)>0)
          echo $callback.'('.$result.');';
        else
          echo $result;
      }

      $this->chan->close();
      $this->conn->close();

    }

    public function bearer() {
      $a = $this->header['authorization'];
      if (strlen($a)>2) {
        preg_match("/^Bearer\s+(.*)$/i",$a,$b);
        if (count($b)==2) {
          $this->bearer = $b[1];
          return $b[1];
        }
      }
      return null;

    }


    public function result() {
      if ($this->de)  $this->debug($this->j);
      $result = json_encode($this->j);
      $callback = $this->cb;
      if (strlen($callback)>0)
        echo $callback.'('.$result.');';
      else
        echo $result;
    }

    public function debug($b) {

      $j = array();
      $j['uri'] = $this->s['PHP_SELF']; //getcwd(), __FILE__, dirname()
      $j['ipa'] = $this->addr;
      if (is_array($b)) {
        $j['head'] = $this->header;
        $j['req'] = $this->req;
        $j['body'] = $this->jwt();
        $j['res'] = $b;
      } else
        $j['str'] = $b;

      $this->chan->basic_publish(new AMQPMessage(json_encode($j)), 'ipay', '/debug');
    }   
  }


  class uK
  {
    static public function debug($b) {
      $conn = new AMQPStreamConnection('10.0.75.136', 5672, 'yuki', '1112', '/');
      $chan = $conn->channel();

      $j = array();
      $j['uri'] = $_SERVER['PHP_SELF']; //getcwd(), __FILE__, dirname()
      $j['ipa'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
      if (is_array($b)) {
        $j['req'] = $_REQUEST;
        $j['res'] = $b;
      } else
        $j['str'] = $b;

      $chan->basic_publish(new AMQPMessage(json_encode($j)), 'ipay', '/debug');
      $chan->close();
      $conn->close();
    }   

  static function obj2arr($d) {
    if (is_object($d))
      $d = get_object_vars($d);
    if (is_array($d))
      return array_map(__FUNCTION__, $d);
    else
      return $d;
  }

  static function randnum($n) {
    $key = "";
    $str = "0123456789";
    $slen = strlen($str);
    for ($i=0;$i<$n;$i++) {
      //$b[$i] = $str[rand(0,$slen-1)];
      $key .= $str[rand(0,$slen-1)];
    }
    return $key;
  }

  static function randchar($n) {
    $key = "";
    //$str = "abcdefghijklmnopqrstuvwxyz";
    $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $slen = strlen($str);
    for ($i=0;$i<$n;$i++) {
      //$b[$i] = $str[rand(0,$slen-1)];
      $key .= $str[rand(0,$slen-1)];
    }
    return $key;
  }

  static function renum() {
    return uK::randchar(2).uK::randnum(4);
  }


  static function tkgen($rdate) {
    $str = "abcdefghijklmnopqrstuvwxyz";
    $slen = strlen($str);
    $a=0;
    $t=0;
    for ($i=0;$i<10;$i++) {
      $a = rand(0,$slen-1);
      $t += $a;
      $b[$i] = $str[$a]; // substr($str,$a[i],$a[i]);
    }
    $k = $str[($t%$slen)];
    $key = $b[7].substr(date("Y",$rdate),0,2).$b[8].$b[9]."-".$b[0].$b[1].date("y",$rdate).$b[2]."-".date("m",$rdate).$b[3].date("d",$rdate)."-".date("H",$rdate).$b[4].date("i",$rdate)."-".$b[5].date("s",$rdate).$b[6].$k;
    return $key;
  }

  static function tkchk($code) {
    if (strlen($code)==29) {
      $str = "abcdefghijklmnopqrstuvwxyz";
      $slen = strlen($str);
      $a=0;
      $t=0;
      preg_match("/^([a-z])[0-9][0-9]([a-z])([a-z])-([a-z])([a-z])[0-9][0-9]([a-z])-[0-9][0-9]([a-z])[0-9][0-9]-[0-9][0-9]([a-z])[0-9][0-9]-([a-z])[0-9][0-9]([a-z])([a-z])$/",$code,$b);
      for ($i=1;$i<=10;$i++) {
        $a = strpos($str,$b[$i]);
        $t += $a;
      }
      $k = $str[($t%$slen)];
      if ($k==$b[11])
        return true;
      else
        return false;
    } else
      return false;
  }

  static function getPost($a) {
    $t = "";
    if ($a!=null)
      $t = str_replace("'","''",$a);
    return $t;
  }

  static function getNumber($a) {
    $a = trim(uK::getPost($a));
    if (is_numeric($a))
      return ($a);
    else
      return (-1);
  }



  }

  //class mx extends MongoDB
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


?>
