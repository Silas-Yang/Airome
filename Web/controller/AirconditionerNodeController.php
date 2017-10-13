<?php
	// 开关节点控制器
	// web/controller/SwitchNodeController.php
	// Ylh, 2015-07-19
	require_once _CONTROLLER_ . "/NodeController.php";
	require_once _LIB_ . "/SendCommandLib.php";
	class AirconditionerNodeController extends NodeController{
		// @author ylh
		// @parameter 
		// @return number
		public function changeValue($node_id="", $node_value="", $ajax=true){
			if($ajax){
				$node_value = $_POST['node_value'];
				$node_id = $_POST['node_id'];
			}
			$this->node->initModel($node_id);
			$this->node->data['node_value'] = $node_value;
			if(!$this->node->updateModel()){
				if($ajax) echo '0';
				return 0;
			}
			$res = SendCommandLib::sendToNode($node_id, $this->node->data['node_value']);
			if($ajax == true) echo $res;
			return $res;
		}
	}