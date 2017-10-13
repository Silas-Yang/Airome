<?php
	// 该文件用于定义是否需要登陆访问控制器方法
	// 定义样例如下：
	// define("UserController_login", false); 
	// 格式为：define("控制器_方法名", false|true)
	// 为false即为不需登陆就可访问，不定义或者定义为true为需要登陆

// UserController类
	define("UserController_login", false); // 用户登录
	define("UserController_isExisted", false); // 判断用户存在与否
	define("UserController_register", false); // 用户注册
	

// ValidateCodeController类
	define("ValidateCodeController_doimg", false); // 验证码
	define("ValidateCodeController_verify", false); // 验证码的验证函数
