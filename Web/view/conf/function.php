<?php
	// web\view\conf\function.php
	// 用于加载视图层的公共函数
	
	function F($str){
		return htmlentities($str,ENT_QUOTES,"UTF-8");
	}