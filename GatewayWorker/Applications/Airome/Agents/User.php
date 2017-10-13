<?php
  use \GatewayWorker\Lib\Gateway;
  use \GatewayWorker\Lib\Db;
/**
* Users request service
*/
function userRequest($client_id, $data){
    echo "user request\n=========\n";
	if(!isset($data["user_id"], $data['password'])){
		Gateway::sendToClient("missing param");
		Gateway::closeCurrentClient();
	}
	$user_id = addslashes($data['user_id']);
	// verify user password.
	$db = Db::instance('airome');
   	if($data['password'] !=  $db->single("SELECT `password` FROM `user` WHERE `user_id`='$user_id'")){	
		Gateway::sendToClient("verifying failed");
  		Gateway::closeCurrentClient();
  	}
  	// get the arduino gateway's client_id
  	$gateway_client_id = $db->single("SELECT `node_value` FROM `node` WHERE `node_id`=(SELECT `gateway_id` FROM `node` WHERE `node_id`='".$data['node_id']."')");
  	if($gateway_client_id==null || !Gateway::isOnline($gateway_client_id)){
  		Gateway::sendToCurrentClient("not online");
  		Gateway::closeCurrentClient();
  	}
      switch ($data['type']) {
      case 'to node':
          $to_send = "to node\n" . "node_id:".$data['node_id']."\nnode_value:" . $data['node_value']."\n";
          echo "to node";
          Gateway::sendToClient($gateway_client_id,$to_send);
          Gateway::sendToCurrentClient("done");
          Gateway::closeCurrentClient();
          break;
      default:
          Gateway::sendToCurrentClient("unknown");
          break;
      }
  	echo "\n=========\n";
  }
