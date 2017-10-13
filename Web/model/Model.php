<?php
	// 文件	：/web/model/Model.php
	// 描述	：模型
	// ylh,2015-04-05
	require_once _CONNECT_DB_;//需要连接数据库
	class Model{
		protected $PK;//主键名
		protected $Table;//表名
		public $data;
		private $flag=false;//标记是否获取实体
	
		//获取特定id的实体
		function initModel($id){
			$id=mysql_real_escape_string($id);
			$result=mysql_query("SELECT * FROM `$this->Table` WHERE `$this->PK`='$id'");
			$row=mysql_fetch_array($result);
			if($row){
				$this->flag=true;
				foreach ($row as $key => $value) {
					if(!is_numeric($key)&&$value!=NULL)
						$this->data["$key"]=$value;
				}
				return true;
			}
			else
				return false;
		}

		//更新实体
		function updateModel(){
			//判断是否获取实体
			if(!$this->flag)
				return false;
			$VALUES='';
			foreach ($this->data as $key => $value) {
					$value = mysql_real_escape_string($value);
					$VALUES.=",`$key`='$value'";
			}
			$VALUES = substr($VALUES, 1);//去掉最前面的逗号...
			$ID=$this->data[$this->PK];
			if(mysql_query("UPDATE `$this->Table` SET $VALUES WHERE $this->PK='$ID'"))
				return true;
			else
				return false;
		}

		//插入新实体
		function addModel(){
			if($this->flag)//如果已经获取了实体，则不允许该实体再次添加。
				return false;
			$FIELDS='';
			$VALUES='';
			foreach ($this->data as $key => $value) {
				$value = mysql_real_escape_string($value);
				$FIELDS .= ",`$key`";
				$VALUES .= ",'$value'";
			}
			$FIELDS = substr($FIELDS, 1);//去掉最前面的逗号...
			$VALUES = substr($VALUES, 1);//去掉最前面的逗号...
			if(mysql_query("INSERT INTO `$this->Table` ($FIELDS) VALUES ($VALUES)"))
				return true;
			else
				return false;
		}

		//删除该实体
		function delModel(){
			if(!$this->flag)//如果没有获取该实体，则返回false;
				return false;
			$ID = $this->data[$this->PK];
			if(mysql_query("DELETE FROM `$this->Table` WHERE `$this->PK`='$ID'"))
				return true;
			else
				return false;
		}

		//获取某个id的某个字段
		function getField($ID, $field){
			$ID = mysql_real_escape_string($ID);
			$field = mysql_real_escape_string($field);
			$result = mysql_query("SELECT `$field` FROM `$this->Table` WHERE `$this->PK`='$ID'");
			$row = mysql_fetch_array($result);
			return $row["$field"];
		}

	}