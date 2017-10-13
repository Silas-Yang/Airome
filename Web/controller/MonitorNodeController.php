<?php
	// 开关节点控制器
	// web/controller/MonitorNodeController.php
	// Ylh, 2015-08-22
	require_once _CONTROLLER_ . "/NodeController.php";
	require_once _LIB_ . "/SendCommandLib.php";
	class MonitorNodeController extends NodeController{
		function getData($node_id =  "", $ajax = true){
			if($ajax == true){
				$node_id  = $_POST['node_id'];
			}
			SendCommandLib::sendToNode($node_id, "getData");
			$node_value = $this->node->getField($node_id, "node_value");
			$tmp = explode(",", $node_value);
			$result['temperature'] = $tmp[0];
			$result['humidity'] = $tmp[1];
			if($ajax) echo json_encode($result);
			return $result;
		}
	}