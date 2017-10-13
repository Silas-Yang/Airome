<?php
	//File: /web/controller/CommandController.php
	//Description: Command Controller
	//Author: S·Fermi
	require_once _MODEL_ ."/CommandModel.php";
	class CommandController{
		private $command;
		function __construct()
		{
			$this->command = new CommandModel();
		}

		function isExisted($commandid,$ajax=true)
		{
			if ($this->user->getField($commandid,"command_id")==null)
			{
				if($ajax) echo '0';
				return false;
			}
			else
			{
				if($ajax) echo '1';
				return true;
			}
		}
		function getCommandStatus($commandid,$ajax=true)
		{
			if($ajax) echo $this->command->getField($commandid,"command_status");
			return $this->command->getField($commandid,"command_status");
		}
		function addCommand($ajax=true)
		{
			if(!isset($_COOKIE['user_id'],$_COOKIE['node_id'],$_COOKIE['gateway_id'],$__COOKIE['command_value'])
				||$_COOKIE['user_id']==""
				||$_COOKIE['node_id']==""
				||$_COOKIE['gateway_id']==""
				||$__COOKIE['command_value']=="")
			{
				if($ajax) echo '0';
				return 0;
			}
			$this->command->data['user_id'] = $_COOKIE['user_id'];
			$this->command->data['node_id'] = $_COOKIE['node_id'];
			$this->command->data['gateway_id'] = $_COOKIE['gateway_id'];
			$this->command->data['command_value'] = $__COOKIE['command_value'];
			if(!$this->command->addModel())
			{
				if($ajax) echo '0';
				return 0;
			}
			else
			{
				if($ajax) echo '1';
				return 1;
			}
		}
	}