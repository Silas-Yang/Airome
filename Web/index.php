<?php
	// 文件	：/web/index.php
	// 描述	：用于控制视图，视图后缀为html，只需为参数v赋值视图名即可（无需后缀）
	// 		  载入web下的变量。
	// 		  用于调用控制器方法，c参数为控制器，f为类方法。
	// ylh,2015-04-03
	require_once _WEB_ . "/conf/const.php";
	include_once _CONTROLLER_ . "/UserController.php";
	$userCon = new UserController();
	if(isset($_GET['c'], $_GET['f'])){//调用的控制器和函数
		include_once _CONTROLLER_ . "/conf/conf.php";
		if(!defined($_GET['c'] . "Controller_" . $_GET['f']) || constant($_GET['c'] . "Controller_" . $_GET['f'])==true){
			//为真则需要登陆才能访问
			if($userCon->isLogined()==true){
				include_once _CONTROLLER_ . "/". $_GET['c'] . "Controller.php";
				include_once _CONTROLLER_ . "/conf/conf.php";
				$Controller = $_GET['c'] . "Controller";
				(new $Controller)->$_GET['f']();
			}
			else die('please login');
		}
		else{
			//不需要登陆即可访问：
			include_once _CONTROLLER_ . "/". $_GET['c'] . "Controller.php";
			include_once _CONTROLLER_ . "/conf/conf.php";
			$Controller = $_GET['c'] . "Controller";
			(new $Controller)->$_GET['f']();
		}
	}
	else{//若没有调用控制器和函数
		include_once _VIEW_ . "/conf/conf.php";
		include_once _VIEW_ . "/conf/function.php";
		if(!isset($_GET['v'])){//若未指定视图，跳转到主页
			$_GET['v']='device_list';
			$page = _VIEW_ . '/device_list.html';
		}
		else{//指定了视图
			$page = _VIEW_ . '/' . $_GET['v'] . '.html';
		}

		///输出页面
		if(!file_exists($page)){
			include _VIEW_ . '/404.html';//404页面
		}
		else{
			//检查是否需要登陆访问
			//当常量未定义则为需登录
			//当常量已定义且其值为真时，需登录
			//即，只有当常量定义为false时，才可不登陆访问
			if(!defined($_GET['v']) || constant($_GET['v'])==true){//为真则需要登陆访问
				if($userCon->isLogined()==true)
					include $page;//页面存在，输出该页面（此html可嵌入php）
				else
					include _VIEW_ . '/forbidden.html';
			}
			else{// 不需要登陆访问时：
				include $page;
			}
		}
	}