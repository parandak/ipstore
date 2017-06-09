<?php

  require_once '/home/nfs/vendor/autoload.php';
  use PhpAmqpLib\Connection\AMQPStreamConnection;
  use PhpAmqpLib\Message\AMQPMessage;
  use PhpAmqpLib\Wire\AMQPTable;
  use PhpAmqpLib\Exception\AMQPTimeoutException;
  use GuzzleHttp\Client;
  use \Firebase\JWT\JWT;

class tt
  {
    var $cli = null;

    function __construct() {
      $this->cli = new Client([
        'base_uri' => 'https://api.instapay.kr/s1/',
        'timeout' => 3.0
      ]);
    }

    public function api($j) {
      $req = [
        'headers' => [
          'User-Agent' => 'InstaPAY/1.0',
          'Authorization' => 'Bearer: AAAAAAAAAAAAAAAABCDEFGHIJKOKL'
        ],
        'body' => $j
      ];

      $res = $this->cli->request('GET', "b?id=skjkfjskdfj&ba=kjfkjkdjf", $req);
      return $res->getBody();
    }
  }


$req = [
      'headers' => [
        'User-Agent' => 'InstaPAY/1.0',
        'Authorization' => 'Bearer: ABCDEFGHIJKOKL',
      ],
      'form_params' => [
        'client_id' => 'l7xx212c488f11324fb582460199731faef6',
        'client_secret' => '97cba127e0954c4a848dccdcc804da4d',
        'grant_type' => 'authorization_code',
        'redirect_uri' => 'https://api.instapay.kr/k1/kfcback'
      ]
    ];


$j = array();
$j['a'] = "1111";
$j['b'] = "1111";
$j['c'] = "한글1111";
$j = json_encode($j);


      $k = new tt();
      $a = $k->api($j);

    echo $a;


?>
