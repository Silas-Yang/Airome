<?php
	// 开关节点控制器
	// web/controller/SwitchNodeController.php
	// Ylh, 2015-05-21
	require_once _CONTROLLER_ . "/NodeController.php";
	require_once _LIB_ . "/SendCommandLib.php";
	class SwitchNodeController extends NodeController{

		// 打开开关 
		// 返回值：1成功，0失败,  -1 cannot create socket or connect Worker Server.
		function on($node_id="", $ajax=true){
			if($ajax==true){
				$node_id = $_POST['node_id'];
			}
			$this->node->initModel($node_id);
			$this->node->data['node_value'] = 1;
			if($this->node->updateModel()==false){
				if($ajax==true) echo "0";
				return 0;
			}

			$res = SendCommandLib::sendToNode($node_id, $this->node->data['node_value']);
			if($ajax == true) echo $res;
			return $res;
		}

		// 关闭开关 
		// 返回值：1成功，0失败
		// ylh
		function off($node_id="", $ajax=true){
			if($ajax==true){
				$node_id = $_POST['node_id'];
			}
			$this->node->initModel($node_id);
			$this->node->data['node_value'] = 0;
			if($this->node->updateModel()==false){
				if($ajax==true) echo "0";
				return 0;
			}

			$res = SendCommandLib::sendToNode($node_id, $this->node->data['node_value']);
			if($ajax == true) echo $res;
			return $res;
		}

		// 获取开关状态
		// 返回值：0关闭，1打开
		// ylh
		function getStatus($node_id="", $ajax=true){
			if($ajax==true){
				$node_id = $_POST['node_id'];
			}
			$status = $this->node->getField($_POST['node_id'],"node_value");
			if($ajax==true) echo $status;
			return $status;
		}
	}