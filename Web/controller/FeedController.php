<?php
	// 文件	：/web/controller/FeedController.php
	// 描述	：用户控制器
	// ljx,2015-07-18
	require_once _MODEL_ . "/FeedModel.php";
	class FeedController{
		private $feedback;
		function __construct(){
			$this->feedback=new FeedModel();
		}
		
		//反馈
		// 1:成功, 2:模型错误
	
		function addFeed($ajax=true){
			$content  			=	$_POST['content'];
			$user_id  			=	$_COOKIE['user_id'];	
			
			$this->feedback->data['user_id'] 	=	$user_id;
			$this->feedback->data['content'] 	=	$content;
		
				if(!$this->feedback->addModel()){
					if($ajax) echo '2'; 
					return 2;//2,模型错误
				}
				else{
					if($ajax)
						echo "1";
					return 1;//1,反馈成功
				}
			}
	}