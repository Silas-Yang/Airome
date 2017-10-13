<?php
  /**
  * @author ylh<ruo92@vip.qq.com>
  * @copyright airome
  */
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;

class Event{
  /**
  * 当客户端连接时触发
  * 如果业务不需此回调可以删除onConnect
  * 
  * @param int $client_id 连接id
  * @link http://gatewayworker-doc.workerman.net/gateway-worker-development/onconnect.html
  */
  public static function onConnect($client_id)
  {
    echo $client_id . " connected.\n";
  }

  /**
  * 当客户端发来消息时触发
  * @param int $client_id 连接id
  * @param string $message 具体消息
  * @link http://gatewayworker-doc.workerman.net/gateway-worker-development/onmessage.html
  */
  public static function onMessage($client_id, $message){
    $data = json_decode($message,true);
    var_dump($message);
    if($message == false){
      Gateway::closeCurrentClient();
    }
    switch ($data['agent']) {
      case 'user':
          require_once __DIR__ . "/Agents/User.php";
          userRequest($client_id,$data);
          break;
      case 'node':
          require_once __DIR__ . "/Agents/Node.php";
          nodeRequest($client_id,$data);
          break;
      default:
         // Gateway::closeCurrentClient();
    }
  }

  /**
  * 当用户断开连接时触发
  * @param int $client_id 连接id
  */
  public static function onClose($client_id)
  {
    echo $client_id . " closed.\n";
  }
}
