<?php
	require_once _MODEL_ . "/NodeModel.php";
	class NodeController{
		protected $node;
		
		function __construct(){
			$this->node=new NodeModel();
		}
		
		//判断node_id是否存在, 0为不存在，1为存在
		function isExisted($nodeid, $ajax=true){
			if($this->node->getField($nodeid, "node_id")==null){
				if($ajax) echo '0';
				return false;
			}
			else {
				if($ajax) echo '1';
				return true;
			}
		}
		
		// 设置节点名字
		// 返回值：
		//		1	:	成功
		// 		0	:	失败
		// 	   -1	:	该节点不属此用户
		function setNodeName($node_id="", $user_id='', $node_name='', $ajax=true){
			if($ajax==true){
				$node_id=$_POST['devID'];
				$user_id=$_POST['userID'];
				$node_name=$_POST['devName'];
			}
			if($this->isExisted($node_id,false)==true){
				$this->node->initModel($node_id);
				if($this->node->data['user_id']==$user_id){
					$this->node->data["node_name"]=$node_name;
					$this->node->updateModel();
					if($ajax) echo '1';
					return 1;
				}
				else {
					if($ajax) echo '-1';
					return -1;
				}
			}
			else {
				if($ajax) echo '0';
				return 0;
			}
		}
		
		// 设置node_name,设置用户激活状态为1
		// 返回值：
		// 			1:激活成功
		// 			0:密码错误
		// 		   -1:不存在该设备
		// 		   -2:已激活该设备
		function activate($node_id='', $node_password='', $node_name='', $group_id='', $gateway_id='', $ajax=true){
			if($ajax==true){
				$node_id		=	$_POST['node_id'];
				$node_password	=	$_POST['node_password'];
				$node_name		=	$_POST['node_name'];
				$group_id		=	$_POST['group_id'];
				$gateway_id 	=	$_POST['gateway_id'];
			}
			if($this->isExisted($node_id,false)==true){
				$this->node->initModel($node_id);
				// 检查是否已经被激活
				if($this->node->data['node_status']==1){
					if($ajax==true) echo "-2";
					return -2;
				}
				// 检查密码是否匹配
				if($this->node->data['node_password']==$node_password){
					$this->node->data['node_status']	 =	1;
					$this->node->data['node_name']		 =	$node_name;
					$this->node->data['node_group_id']	 =	$group_id;
					$this->node->data['gateway_id'] 	 =	$gateway_id;
					$this->node->data['node_reg_date']	 =	date("Y-m-d H:i:s",time());
					$this->node->updateModel();
					if($ajax==true) echo "1";
					return 1;
				}else{
					if($ajax==true) echo "0";
					return 0;
				}
			}else{
				if($ajax==true) echo "-1";
				return -1;
			}
		}

		// 函数：禁用设备
		// 参数：
		// 		NodeIDs 	:	要禁用的设备ID
		// 		gatewayID	:	网关ID
		// 返回值：
		// 		1	： 	禁用成功
		// 		0	： 	禁用失败
		// 		-1	:	设备所属网关ID不匹配
		// 		-2	:	设备不存在
		// Author:ylh,2015-05-16		
		function disableNode($NodeIDs=array(),$gatewayID="",$ajax=true){
			if($ajax==true){
				$NodeIDs	=	$_POST['device'];
				$gatewayID 	= 	$_POST['gatewayID'];
			}
			// 遍历要删除的设备数组
			foreach ($NodeIDs as $key => $value) {
				// 初始化改实体
				if($this->node->initModel($value)==true){
					// 比对设备所属网关ID是否匹配，若不匹配则返回-1
					if($this->node->data['gateway_id']!=$gatewayID){
						if($ajax==true) echo "-1";
						return -1;
					}
					// 修改状态为2，即已禁用
					$this->node->data['node_status'] = 2;
					$this->node->data['node_group_id'] = 0;
					$this->node->data['gateway_id'] = $value;
					if($this->node->updateModel()!=true){
						if($ajax==true) echo "0";
						return 0;
					}
				}else{//初始化失败，即分组不存在时
					if($ajax==true) echo "-2";
					return -2;
				}
			}
			//经历了上面这么多还没有出错的话，那么就成功了吧^_^
			if($ajax==true) echo "1";
			return 1;
		}
		
		// 验证节点密码
		// 返回值
		// 			1:正确
		// 			0:错误
		function verifyPassword($node_id='', $node_password='', $ajax=true){
			if($ajax==true){
				$node_id		=	$_POST['node_id'];
				$node_password	=	$_POST['node_password'];
			}
			if($this->node->getField($node_id,"node_password")==$node_password){
				if($ajax==true) echo "1";
				return 1;
			}else{
				if($ajax==true) echo "0";
				return 0;
			}
		}

		// 获取节点类型
		// 返回值：数字
		function getType($node_id){
			return $this->node->getField($node_id, "node_type");
		}

		// 函数：获取设备
		// 参数：
		// 	UserID 	 :	设备所属的用户ID
		// 	Password :	用户的密码
		// 返回值：
		// 		数组或JSON数据
		// 		0:密码错误
		// 	   -1:无权查看该设备
		function getNodes($UserID="", $Password="", $GroupID="", $ajax=true){
			if($ajax==true){
				$UserID 	=	 $_POST['user_id'];
				$Password 	=	 $_POST['password'];
				$GroupID 	=	 $_POST['group_id'];
			}

			//如果是其他控制器调用则需要检查用户密码
			if($ajax==false){
				include_once _CONTROLLER_ . "/UserController.php";
				$user = new UserController();
				if($user->verifyUser($UserID, $Password, false)==0){
					return 0;
				}
			}

			// 判断该分组是否属于该用户
			require_once _MODEL_ . "/GroupModel.php";
			$group = new GroupModel();
			if($UserID != $group->getField($GroupID,"group_uid")){
				if($ajax==true) echo "-1";
				return -1;
			}

			$nodes = $this->node->getNodes($GroupID);
			if($ajax==true){
				echo json_encode($nodes);
			}
			return $nodes;
		}

		// 重命名设备
		// 参数
		// 		$node_id 	:	设备ID
		// 		$gateway_id	:	要修改的设备的gatewayID
		// 		$node_name	:	要修改成的名字
		// 返回值
		// 			1:成功
		// 			0:失败
		// 		   -1:该设备id与网关id不匹配
		// 		   -2:设备不存在		
		function renameNode($node_id="", $gateway_id="", $node_name="",$ajax=true){
			if($ajax==true){
				$node_id 	=	$_POST['node_id'];
				$gateway_id  	=	$_POST['gateway_id'];
				$node_name	=	$_POST['node_name'];
			}

			if($this->node->initModel($node_id)==false){
				if($ajax==true) echo "-2"; // 初始化失败, 说明不存在该分组
				return 2;
			}

			if($this->node->data['gateway_id']!=$gateway_id){
				if($ajax==true) echo "-1"; // 与网关id不匹配
				return -1;
			}

			$this->node->data['node_name'] = $node_name;
			if($this->node->updateModel()==false){
				if($ajax==true) echo "0";
				return 0;
			}

			if($ajax==true) echo "1";
			return 1;
		}

	}