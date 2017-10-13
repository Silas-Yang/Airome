//By  Y.lh, Email: ruo92@vip.qq.com

#include "AiromeClient.h"

AiromeClient::AiromeClient(){
      Serial.begin(9600);
      
}

void AiromeClient::begin(){
    byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0xFE, 0xED };
      Serial.println("Trying configure Ethernet using DHCP...");
     if( !Ethernet.begin(mac) )
       Serial.println("Failed to configure Ethernet by DHCP");
}

int AiromeClient::post(char* host_name,char* path, String data, String& response){
	// if there's a successful connection:
//  Serial.println(host_name);
//  Serial.println(path);
//  Serial.println(data);
  if (client.connect(host_name, 80)) {
    Serial.println("connected.");
    // send the HTTP POST request:
    client.print("POST ");
    client.print(path);
    client.println(" HTTP/1.1");
    client.print("Accept:text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/");
    client.println("*;q=0.8");
    client.println("Accept-Language:zh-CN,zh;q=0.8");
//    client.println("Cache-Control:max-age=0");
    client.println("Connection:close");
    client.print("Content-Length:");
    client.println(data.length());//14");
    client.println("Content-Type:application/x-www-form-urlencoded");
    
    client.print("Host:");
    client.println(host_name);
//    client.println("Origin:null");
    client.println("");
    client.println(data);
  } 
  else {
    // if you couldn't make a connection:
    Serial.println("connection failed");
    Serial.println("disconnecting.");
    client.stop();
    return 0;//return failed;
  }
  Serial.println("getting...");
  // delay(100);//wait for request;

  response = "";
  String str = "";
  unsigned long time = millis();
  while(!client.available()){
    if(millis() - time >500){
      Serial.println("WebServer Timeout.");
      client.stop();
      return 0;// return failed;
    }
  }
  while(client.available()){
  	str += (char)client.read();
  }
  client.stop();
  // Serial.print(str);
  // Serial.print("the index of '\\n' is: ");
  int index = str.indexOf("\r\n\r\n");
  // Serial.println(index+4);
  if(index != -1)
    response = str.substring(index+4);
  
  return 1;
  #ifdef __DEBUG__
    //reserved for debugging code...  :)
  #endif

}
