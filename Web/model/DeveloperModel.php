<?php
	// 文件	：/web/model/DeveloperModel.php
	// 描述	：节点模型
	// ylh,2015-04-06
	require_once _MODEL_ . "/Model.php";
	class CommandModel extends Model {
		function __construct(){
			$this->PK="dev_id";
			$this->Table="developer";
		}
	}