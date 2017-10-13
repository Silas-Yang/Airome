<?php
	// 文件	：/web/controller/GroupController.php
	// 描述	：分组控制器
	// ylh,2015-05-08
	require_once _MODEL_ . "/GroupModel.php";
	class GroupController{
		private $group;
		function __construct(){
			$this->group=new GroupModel();
		}

		// 函数：添加分组
		// 参数：
		// 	GroupName: 	欲增加的分组名
		// 	UserID 	:	分组所属的用户ID
		// 	Password :	用户的密码
		// 返回值:
		// 		-1:参数为空
		// 		 1:成功
		//		 0:密码或用户名错误
		//		-2:其他错误
		function addGroup($GroupName="",$UserID="",$Password="",$ajax=true){
			if($ajax==true){
				$GroupName = $_POST['group_name'];
				$UserID = $_COOKIE['user_id'];
				$Password = $_COOKIE['password'];
			}

			if($GroupName==""||$UserID==""||$Password==""){
				if($ajax==true)
					echo "-1";
				return -1;
			}

			//如果是其他控制器调用则需要检查用户密码
			if($ajax==false){
				include_once _CONTROLLER_ . "/UserController.php";
				$user = new UserController();
				if($user->verifyUser($UserID, $Password, false)==0){
					return 0;
				}
			}
			
			$this->group->data['group_name'] = $GroupName;
			$this->group->data['group_uid'] = $UserID;

			if($this->group->addModel()){
				if($ajax==true)
					echo "1";
				return 1;
			}else{
				if($ajax==true)
					echo "-2";
				return -2;
			}
		}

		// 函数：获取分组
		// 参数：
		// 	UserID 	 :	分组所属的用户ID
		// 	Password :	用户的密码
		// 返回值：
		// 		数组或JSON数据
		function getGroups($UserID="", $Password="", $ajax=true){
			if($ajax==true){
				$UserID = $_COOKIE['user_id'];
				$Password = $_COOKIE['password'];
			}

			//如果是其他控制器调用则需要检查用户密码
			if($ajax==false){
				include_once _CONTROLLER_ . "/UserController.php";
				$user = new UserController();
				if($user->verifyUser($UserID, $Password, false)==0){
					return 0;
				}
			}

			$groups = $this->group->getGroups($UserID);

			if($ajax==true){
				echo json_encode($groups);
			}
			return $groups;
		}

		// 函数：删除分组
		// 参数：
		// 		GroupID 	:	要删除的分组ID
		// 		UserID 		:	用户ID
		// 返回值：
		// 		1	： 	删除成功
		// 		0	： 	删除失败
		// 		-1	:	分组所属用户名不匹配
		// 		-2	:	分组不存在
		// 		-3	:	分组下还存在节点，不能删除
		// Author:ylh,2015-05-08
		function delGroup($DelGroupID=array(),$UserID="", $ajax=true){
			if($ajax==true){
				$DelGroupID = $_POST['group'];
				$UserID = $_COOKIE['user_id'];
			}
			// 遍历要删除的分组数组
			foreach ($DelGroupID as $key => $value) {
				// 初始化改实体
				if($this->group->initModel($value)==true){
					// 比对分组所属用户ID是否匹配，若不匹配则返回-1
					if($this->group->data['group_uid']!=$UserID){
						if($ajax==true) echo "-1";
						return -1;
					}
					// 判断该分组下是否还有节点，有节点则不能删除
					if($this->group->getNumOfNodes($value)!=0){
						if($ajax == true) echo "-3";
						return -3;
					}
					// 判断是否成功删除，失败返回0，成功返回1
					if($this->group->delModel()!=true){
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

		// 重命名分组
		// 参数
		// 		$group_id 	:	分组ID
		// 		$user_id 	:	要修改的分组的用户ID
		// 		$group_name	:	要修改成的名字
		// 返回值
		// 			1:成功
		// 			0:失败
		// 		   -1:该分组与用户id不匹配
		// 		   -2:分组不存在		
		function renameGroup($group_id="", $user_id="", $group_name="",$ajax=true){
			if($ajax==true){
				$group_id 	=	$_POST['group_id'];
				$user_id  	=	$_POST['user_id'];
				$group_name	=	$_POST['group_name'];
			}

			if($this->group->initModel($group_id)==false){
				if($ajax==true) echo "-2"; // 初始化失败, 说明不存在该分组
				return 2;
			}

			if($this->group->data['group_uid']!=$user_id){
				if($ajax==true) echo "-1"; // 与用户id不匹配
				return -1;
			}

			$this->group->data['group_name'] = $group_name;
			if($this->group->updateModel()==false){
				if($ajax==true) echo "0";
				return 0;
			}

			if($ajax==true) echo "1";
			return 1;
		}

		// 改变分组折叠状态
		// 返回值
		// 			1:成功
		// 			0:update失败
		//			-1: Wrong uid
		//			-2:init failed
		function changeStatus(){
			$group_status = $_POST['group_status'];
			$group_id 	= $_POST['group_id'];
			$group_uid	= $_COOKIE['user_id'];
			// check uid
			if($this->group->getField($group_id,"group_uid")!=$group_uid){
				echo "-1" . " $group_uid ".$this->group->getField($group_id,"group_uid");
				return;
			}
			// 初始化实体
			if($this->group->initModel($group_id) ==false){
				echo "-2";
				return;
			}
			$this->group->data['group_status'] = $group_status;
			if($this->group->updateModel()==true){
				echo "1";
				return;
			}else{
				echo "0";
				return;
			}
		}
	}