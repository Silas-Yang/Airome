<?php
	// 文件	：/web/model/NodeModel.php
	// 描述	：节点模型
	// ylh,2015-04-05
	require_once _MODEL_ . "/Model.php";
	class NodeModel extends Model {
		function __construct(){
			$this->PK="node_id";
			$this->Table="node";
		}

		//某个用户某个分组的所有设备
		function getNodes($group_id){
			$result = mysql_query("SELECT * FROM $this->Table WHERE node_group_id='$group_id' and node_status!=0 order by node_order");
			$nodes=array();
			$index=0;
			while($row = mysql_fetch_array($result)){
				$nodes[$index]=$row;
				$index++;
			}
			return $nodes;
		}
	}