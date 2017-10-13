<?php
	// 文件	：/web/controller/UserController.php
	// 描述	：用户控制器
	// ylh,2015-04-07
	require_once _MODEL_ . "/UserModel.php";
	class UserController{
		private $user;
		function __construct(){
			$this->user=new UserModel();
		}

		//判断用户是否存在, 0为用户不存在，1为用户存在
		function isExisted($userid="", $ajax=true){
			if($ajax==true) $userid = $_POST['user_id'];
			if($this->user->getField($userid, "user_id")==null){
				if($ajax) echo '0';
				return false;
			}
			else {
				if($ajax) echo '1';
				return true;
			}
		}

		//验证用户名和密码是否匹配,正确返回1，错误返回0
		function verifyUser($userid, $password, $ajax=true){
			if($password==$this->user->getField($userid,"password")){
				if($ajax) echo '1';
				return 1;
			}
			else{
				if($ajax) echo '0';
				return 0;
			}
		}

		//登陆成功则设置COOKIE.并且返回true
		//在ajax请求中：输出1：为登陆成功，0：用户名不存在，-1：用户名或密码错误
		function login($ajax=true){
			$userid="";
			$password="";
			//判断post上来的用户和密码
			if(isset($_POST['user_id'], $_POST['password'])){
				if($_POST['user_id']=="" && $_POST['password']==""){
					if($ajax) echo '-1';
					return -1; 
				}
				$userid = mysql_real_escape_string($_POST['user_id']);
				$password = mysql_real_escape_string($_POST['password']);
				if($this->verifyUser($userid, $password,false)==1){
					setcookie("user_id",$userid);
					setcookie("password",$password);
					$user_name = $this->getName($userid);
					setcookie("user_name",$user_name);
					if($ajax) echo '1';
					return 1;
				}
				else{
					if($ajax) echo "-1";
					return -1;
				}
			}
			else if(isset($_COOKIE['user_id'], $_COOKIE['password']) && $_COOKIE['user_id']!="" && $_COOKIE['password']!=""){
				$userid = mysql_real_escape_string($_COOKIE['user_id']);
				$password = mysql_real_escape_string($_COOKIE['password']);
				return $this->verifyUser($userid, $password);
			}
			else{//用户或密码为空
				if($ajax) echo '0';
				return 0;
			}
		}

		
		//重置密码
		//在ajax请求中：输出1：修改密码成功，0：新密码或旧密码为空, -1:旧密码不匹配
		function updatePassword($ajax=true){
			$new_password="";
			$old_password="";
			$user_id = $_COOKIE['user_id'];
			
			if( $this->isExisted($user_id,false)==false||
				!isset($_POST['new_password'])||$_POST['new_password']=="" ||
				!isset($_POST['old_password'])||$_POST['old_password']==""
				){
				if($ajax)  echo '0';
				return 0; 
			}
			else{
				$new_password = $_POST['new_password'];
				$old_password = $_POST['old_password'];
				$this->user->initModel($user_id);
				if($this->user->data["password"]==$old_password){
					$this->user->data["password"]=$new_password;
					$this->user->updateModel();
					if($ajax)  echo '1';
					return 1;
				}
				else{  
					echo '-1';
					return -1; 
				}
			}
		}
		
		//重置用户名
		//在ajax请求中：输出1：修改用户名成功，0：修改用户名不成功
		function changeName($ajax=true){
			$user_id = $_COOKIE['user_id'];	
			if( $this->isExisted($user_id,false)==false||
				!isset($_POST['new_user_name'])||$_POST['new_user_name']=="" 
				){
				if($ajax)  echo '0';
				return 0; 
			}
			else{
				$new_user_name = $_POST['new_user_name'];
				$this->user->initModel($user_id);
				$this->user->data["user_name"]=$new_user_name;
				$this->user->updateModel();
				setcookie("user_name",$new_user_name);
				if($ajax)  echo '1';
				return 1;	
			}
		}

		//得到用户名
		//输出1：得到用户名成功，0：得到用户名不成功
		function getName($user_id){
			if(isset($_COOKIE['user_id']) && $_COOKIE['user_id']!="")
				$user_id = $_COOKIE['user_id'];
			if( $this->isExisted($user_id,false)==false){
				return $user_id; 
			}
			else{
				$get_user_name = $this->user->getField($user_id, "user_name");
				return $get_user_name;	
			}
		}
		
		//注册用户
		// -1:用户已存在, 0:资料为空, 1:注册成功, 2:模型错误, 3:验证码错误, 4:网关节点密码账号不匹配
		// 5:不正确的网关ID
		function register($ajax=true){
			if(!isset($_SESSION)) session_start();
			$captcha = "";
			$userid = "";
			$password = "";
			$userName = "";
			$userRegDate = "";
			$gateWayID = "";
			$gateWayPassword = "";
			if(	!isset($_POST['user_id'])	|| 	!isset($_POST['password'])			||
				!isset($_POST['user_name']) ||	!isset($_POST['captcha'])   		||
				!isset($_POST['gateway_id']) ||  !isset($_POST['gateway_password'])	|| 
				$_POST['user_id']==""	    ||  $_POST['password']==""      		||
				$_POST['user_name']==""  	||	$_POST['captcha']==""	    		||
				$_POST['gateway_id']==""	||	$_POST['gateway_password']==""
				){
				if($ajax) echo '0';
				return 0; //必填资料为空，不能注册
			}else{
				$userid 			=	$_POST['user_id'];
				$password 			=	$_POST['password'];
				$userName 			=	$_POST['user_name'];
				$captcha 			=	$_POST['captcha'];
				$gateWayID 			=	$_POST['gateway_id'];
				$gateWayPassword 	=	$_POST['gateway_password'];
			}

			if( (!isset($_SESSION['authnum_session']) || $captcha != $_SESSION['authnum_session']) && $ajax==true){
				echo '3';
				session_destroy(); // 清除session, 防止利用相同验证码批量注册
				return 3;
			}
			session_destroy(); // 清除session, 防止利用相同验证码批量注册
			if($this->isExisted($userid, false)==true){
				if($ajax) echo '-1';
				return -1;
				//-1,用户已存在
			}
			else{
				require_once _CONTROLLER_ . "/NodeController.php";
				$node = new NodeController();
				if($node->getType($gateWayID)!=0){
					if($ajax==true) echo "5";
					return 5;
				}
				if($node->verifyPassword($gateWayID,$gateWayPassword,false)==0){
					if($ajax==true) echo "4";
					return 4;// 节点密码不匹配
				}
				
				$this->user->data['user_id'] 	=	$userid;
				$this->user->data['password'] 	=	$password;
				$this->user->data['user_name']	=	$userName;
				$this->user->data['gateway_id']	=	$gateWayID;
				if(!$this->user->addModel()){
					if($ajax) echo '2'; 
					return 2;//2,模型错误
				}
				else{
					$node->activate($gateWayID, $gateWayPassword, $userid . "'s gateway",0,$gateWayID, false );
					require_once _CONTROLLER_ . "/GroupController.php";
					$group = new GroupController();
					$group->addGroup("默认分组",$userid,$password,false);
					if($ajax) echo '1';
					return 1;//1,用户注册成功
				}
			}
		}

		//判断是否登陆
		function isLogined(){
			if(		!isset($_COOKIE['user_id'], $_COOKIE['password'])
				 || $_COOKIE['user_id']==""
				 || $_COOKIE['password']==""){
				return false;
			}
			
			if(
				$this->isExisted($_COOKIE['user_id'], false)==1
				&&
				$this->verifyUser($_COOKIE['user_id'], $_COOKIE['password'], false)==1
				){
				return true;
			}
			else return false;
		}
	}