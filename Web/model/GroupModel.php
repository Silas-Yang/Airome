<?php
	// 文件	：/web/model/GroupModel.php
	// 描述	：分组模型
	// ylh,2015-05-08
	require_once _MODEL_ . "/Model.php";
	class GroupModel extends Model {
		function __construct(){
			$this->PK="group_id";
			$this->Table="groups";
		}

		// 某个用户的所有分组
		function getGroups($user_id){
			$user_id = mysql_real_escape_string($user_id);
			$result = mysql_query("SELECT * FROM $this->Table WHERE group_uid='$user_id'");
			$groups=array();
			$index=0;
			while($row = mysql_fetch_array($result)){
				$groups[$index]=$row;
				$index++;
			}
			return $groups;
		}

		// 获取某个分组下的节点数量
		function getNumOfNodes($group_id){
			$group_id = mysql_escape_string($group_id);
			$result = mysql_query("SELECT count(*) num from node where node_group_id = '$group_id'");
			$num = mysql_fetch_array($result);
			return $num['num'];
		}
	}