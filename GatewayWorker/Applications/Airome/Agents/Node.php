<?php
use \GatewayWorker\Lib\Gateway;
use \GatewayWorker\Lib\Db;
/**
* Arduino gateway request
*/
function nodeRequest($client_id, $data){
	if(!isset($_SESSION['is_login']) && $data['type'] != 'login' ){
		Gateway::sendToCurrentClient("not login");
		// Gateway::closeCurrentClient();
		return;
	};
	$db = Db::instance("airome");
	switch ($data['type']) {
		case 'login': // need gateway_id, password
			if(!isset($data['values'])){
				Gateway::sendToCurrentClient("error in login");
				Gateway::closeCurrentClient();
				break;
			}
			$value = decodeValues($data['values']);
			// var_dump($value);
			if(!isset($value['password'], $value['gateway_id'])){
				Gateway::sendToCurrentClient("error in login");
				Gateway::closeCurrentClient();
				break;
			}
			$gateway_id = addslashes($value['gateway_id']);
			if($value['password'] == $db->single("SELECT node_password FROM `node` WHERE node_id='$gateway_id'")){
				$_SESSION['is_login'] = 1;
				$_SESSION['gateway_id'] = $gateway_id;
				$db->update("node")->cols(array("node_value"=>$client_id))->where("node_id='$gateway_id'")->query();
				Gateway::sendToCurrentClient("logined");
				// Gateway::sendToCurrentClient("DeviceList");
				echo $client_id . " logined.\n";
			}else{
				Gateway::sendToCurrentClient("error in login");
				Gateway::closeCurrentClient();
			}
			break;
		case 'getDevices':
			echo $_SESSION['gateway_id'] . "\n";
			$gateway_id = $_SESSION['gateway_id'];
			$db->update("node")->cols(array("node_status"=>2))->where("node_id!='$gateway_id' AND gateway_id='$gateway_id'")->query();
			$devices = $db->column("SELECT `node_id` FROM `node` WHERE `gateway_id`='$gateway_id' AND `node_id`!='$gateway_id' ");
			var_dump($devices);
			foreach ($devices as $key => $node_id) {
				$value = "node_id:$node_id\nnode_value:ping";
				Gateway::sendToCurrentClient("to node\n$value");
			}
			break;
		case 'pong': // heartbeat response. do nothing.
			echo "$client_id pong, \$Session['is_login']=" . $_SESSION['is_login']."\n";
			break;
		// case 'loseNode':
		// 	$value = decodeValues($data["values"]);
		// 	$losed_node_id = $value['node_id'];
		// 	echo "the losed node's id is: $losed_node_id\n";
		// 	$db->update("node")->cols(array("node_status"=>2))->where("node_id='$losed_node_id'")->query();
		// 	break;
		// case 'onlineNode':
		// 	$value = decodeValues($data["values"]);
		// 	$online_node_id = $value['node_id'];
		// 	echo "the online node's id is: $online_node_id\n";
		// 	$db->update("node")->cols(array("node_status"=>1))->where("node_id='$online_node_id'")->query();
		// 	break;
		case 'values':
			$value = decodeValues($data['values']);
			switch ($value['type']) {
				case 'pong':
					$online_node_id = $value['node_id'];
					echo "the online node's id is: $online_node_id\n";
					$db->update("node")->cols(array("node_status"=>1))->where("node_id='$online_node_id'")->query();
					break;
				case 'value':
					$node_id = $value['node_id'];
					$node_value = $value['content'];
					echo "the node's value is: $node_value\n";
					$db->update("node")->cols(array("node_value"=>$node_value))->where("node_id='$node_id'")->query();
					break;
					// $values = 
				default:
					# code...
					break;
			}

			break;
		default:
			# code...
			break;
	}
  }

  function decodeValues($values){
  	$values = trim($values);
  	$arr = explode( "\n",$values);
  	$value = array();
  	foreach($arr as $k => $v){
  		$tmp = explode(":", $v);
  		$value[trim($tmp[0])] = trim($tmp[1]);
  	}
  	return $value;
  }