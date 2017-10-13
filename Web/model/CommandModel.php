<?php
	// 文件	：/web/model/CommandModel.php
	// 描述	：节点模型
	// ylh,2015-04-06
	require_once _MODEL_ . "/Model.php";
	class CommandModel extends Model {
		function __construct(){
			$this->PK="command_id";
			$this->Table="command";
		}
	}