<?php
	require_once _LIB_ . "/ConnectLib.php";
	class SendCommandLib{
		// send to node
		//  return
		// 	-1 init failed
		// 	-2 not online
		// 	1 successful
		// 	-3 failed,  other reasons
		static public function sendToNode($node_id, $node_value){
			//  send message to worker server
			$arr = array(
				"agent"		=>	"user",
				"user_id"	=>	$_COOKIE['user_id'],
				"password"	=>	$_COOKIE['password'],
				"node_id"	=>	$node_id,
				"node_value"	=>	$node_value,
				"type"		=>	"to node"
				);
			$send_data = str_split(json_encode($arr), 10);
			$to_send = "";
			foreach ($send_data as $key => $value) {
				$to_send .= base64_encode($value). "\0";
			}
			$to_send .= "\n";
			$con = new ConnectLib();
			if ($con->initConnect() != 1) {
				// if($ajax == true) echo "-1";
				return -1;
			};
			$con->sendData($to_send); 

			// get the message from server;
			$rec = explode("\0",$con->receiveData(true));
			$result = "";
			foreach ($rec as $key => $value) {
				$result .= base64_decode($value);
			}
			switch ($result) {
				case 'not online':
					// if($ajax) echo '-2';
					return -2;				
				case 'done':
					return 1;
					break;
				default:
					// if($ajax) echo '-3';
					return "asdf" . $result;
			}
		}

		// static public function getFromNode($node_id, $node_value){
		// 	//  send message to worker server
		// 	$arr = array(
		// 		"agent"		=>	"user",
		// 		"user_id"	=>	$_COOKIE['user_id'],
		// 		"password"	=>	$_COOKIE['password'],
		// 		"node_id"	=>	$node_id,
		// 		"node_value"	=>	$node_value,
		// 		"type"		=>	"get from node"
		// 		);
		// 	$send_data = base64_encode(json_encode($arr))."\n";
		// 	$con = new ConnectLib();
		// 	if ($con->initConnect() != 1) {
		// 		// if($ajax == true) echo "-1";
		// 		return -1;
		// 	};
		// 	$con->sendData($send_data); 

		// 	// get the message from server;
		// 	$rec = base64_decode($con->receiveData(true));
		// 	switch ($rec) {
		// 		case 'not online':
		// 			// if($ajax) echo '-2';
		// 			return -2;				
		// 		case 'done':
		// 			return 1;
		// 			break;
		// 		default:
		// 			// if($ajax) echo '-3';
		// 			return -3;
		// 	}
		// }
	}