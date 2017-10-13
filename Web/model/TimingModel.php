<?php
	// 文件	：/web/model/TimingModel.php
	// 描述	：用户模型
	// ljx,2015-07-30
	require_once _MODEL_ . "/Model.php";
	class TimingModel extends Model {
		function __construct(){
			$this->PK="timing_id";
			$this->Table="timing";
		}

		//某个用户所有的定时设置
		function getTimings($ajax=true){
			$user_id  			=	$_COOKIE['user_id'];
			$result = mysql_query("SELECT * FROM $this->Table WHERE user_id='$user_id' and node_status=1");
			$timings=array();
			$index=0;
			while($row = mysql_fetch_array($result)){
				$timings[$index]=$row;
				$index++;
			}
			return $timings;
		}
	
	}