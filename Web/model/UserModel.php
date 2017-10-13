<?php
	// 文件	：/web/model/UserModel.php
	// 描述	：用户模型
	// ylh,2015-04-05
	require_once _MODEL_ . "/Model.php";
	class UserModel extends Model {
		function __construct(){
			$this->PK="user_id";
			$this->Table="user";
		}
	}