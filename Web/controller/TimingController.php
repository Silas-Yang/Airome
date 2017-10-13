<?php
	// 文件	：/web/controller/TimingController.php
	// 描述	：定时控制器
	// ljx,2015-07-30
	require_once _MODEL_ . "/TimingModel.php";
	class TimingController{
		private $timing;
		function __construct(){
			$this->timing=new TimingModel();
		}

//定时
		// 1:成功, 2:模型错误
	
		function addTiming($ajax=true){
			$user_id  			=	$_COOKIE['user_id'];
			$repeat  			=	$_POST['repeat'];  //重复的星期	
			$time_h  			= 	$_POST['time_h'];  //定时时间 小时
			$time_m  			 = 	$_POST['time_m'];  //定时时间 小时			
			$node_id  			=	$_POST['node_id']; //定时设备
			
			
			$this->timing->data['user_id'] 	=	$user_id;
			$this->timing->data['repeat'] 	=	$repeat;
			$this->timing->data['time_h'] 	=	$time_h;
			$this->timing->data['time_m'] 	=	$time_m;
			$this->timing->data['node_id'] 	=	$node_id;
		
			if(!$this->timing->addModel()){
				if($ajax)
				    echo '-1'; 
				echo mysql_error(); 
				return -1;//-1,模型错误
			}
			else{
				if($ajax)
					echo "1";
				return 1;//1,设置定时成功
			}
		}

	}