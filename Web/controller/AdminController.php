<?php
	// 文件	：/web/controller/AdminController.php
	// 描述	：管理员控制器,访问需要带密码参数adminpwd,参数action表示执行的动作
	// ylh,2015-05-09
	require_once _MODEL_ . "/NodeModel.php";
	require_once _MODEL_ . "/GroupModel.php";
	require_once _CONTROLLER_ . "/NodeController.php";
	class AdminController{
		function admin(){
			$pwd = $_GET['adminpwd'];
			$action="";
			if($pwd!="airome666"){
				$action = "wrongPwd";
			}else{
				$action = $_GET['action'];
			}
			$this->$action();
		}

		// 例如：http://localhost/?c=Admin&adminpwd=airome666&f=admin&action=addNodes&NodeType=1&NodeQtt=3&NodeOrder=1
		private function addNodes(){
			$node_type = $_GET['NodeType'];
			$node_qtt = $_GET['NodeQtt']; // 要添加的节点数量
			$node_order = $_GET['NodeOrder']; // 要添加的节点数量
			$node = new NodeModel();
			$node_controller = new NodeController();
			for($i = 0; $i < $node_qtt; $i++){
				$node_id =  $this->randStr(5);
				while($node_controller->isExisted($node_id,false)==true){
					$node_id =  $this->randStr(5);
				}
				$node->data['node_password'] = $this->randStr(8);
				$node->data['node_id'] = $node_id;
				$node->data['node_type'] = $node_type;
				$node->data['node_order'] = $node_order;
				if($node->addModel()){
					echo "succeed!";
				}else{
					echo "failed!";
				}
			}
		}

		private function wrongPwd(){
			echo "<strong>Wrong Password</strong>";
		}

		private function randStr($length=8){
			$charset = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			$str="";
			for($i=0;$i<$length;$i++){
				$str.=$charset[rand(0,25)];
			}
			return $str;
		}
	}