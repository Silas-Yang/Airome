<?php
class ConnectLib{
	private $socket;

	/* 
	*建立连接
	*  return 
	* 	0	:	 failed to create socket;
	*	-1 	:	 failed to connect server;
	*	1 	:	 successful; 
	*/
	function initConnect($address="127.0.0.1", $port=6000){
		//创建端口
		$this->socket = @socket_create(AF_INET,SOCK_STREAM,SOL_TCP);
		if($this->socket === false){
			// echo "socket_create() failed:reason: ".socket_strerror(socket_last_error())."\n";
			return 0;
		}

		//建立连接
		$result = @socket_connect($this->socket,$address,$port);
		if($result === false){
			//  echo "socket_connect() failed. \n Reason:($result)".socket_strerror(socket_last_error($this->socket))."\n";
			return -1;
		}
		return 1;
	}

	/* 
	* 发送数据
	*  return the length of data sent
	*/
	function sendData($data){
		return @socket_write($this->socket, $data);
	}

	/* 
	* 接收数据
	*  return the data received.   "" will be return if received nothing.
	*/
	function receiveData($read_a_line = false){
		$out = "";
		$flag = PHP_BINARY_READ;
		if($read_a_line==true){
			$flag = PHP_NORMAL_READ;
		}
		while ($tmp = @socket_read($this->socket, 10, $flag)) {
			$out .= $tmp;
			if(strpos($tmp,"\n")) return $out;
		}
		return $out;
	}

	/* 
	* 关闭连接
	*  return void
	*/
	function closeConnect(){
		socket_close($this->socket);
	}
}