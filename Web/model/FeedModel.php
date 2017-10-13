<?php
	// 文件	：/web/model/FeedModel.php
	// 描述	：用户模型
	// ljx,2015-07-18
	require_once _MODEL_ . "/Model.php";
	class FeedModel extends Model {
		function __construct(){
			$this->PK="feedback_id";
			$this->Table="feedback";
		}
	}