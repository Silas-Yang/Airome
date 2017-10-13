#include <SPI.h>
#include <Ethernet.h>
#include <Base64.h>
#include <RF24.h>
#include "AiromeBase64.h"
#include <EEPROM.h>
#include "AiromeRadio.h"
// Enter a MAC address for your controller below.
// Newer Ethernet shields have a MAC address printed on a sticker on the shield
byte mac[] = { 0xDE, 0xAD, 0xBE, 0xEF, 0x9E, 0xED };
// if you don't want to use DNS (and reduce your sketch size)
// use the numeric IP instead of the name for the server:
IPAddress server(120,25,157,105);  // numeric IP for airome
int port = 6000;
const unsigned long  no_heartbeat_limit_time = 90000; // 10s, if there is no ping in 10s, will reconnect
//char server[] = "airome.ylh.com";    // name address for Airome (using DNS)

// Initialize the Ethernet client library
// with the IP address and port of the server
// that you want to connect to (port 80 is default for HTTP):
EthernetClient *client = new EthernetClient();
// id and password
String gateway_id = "";
String gateway_password = "";

// Initialize the AiromeRadio library
AiromeRadio radio;
void setup(){
  // Open serial communications and wait for port to open:
  Serial.begin(9600);
  // start the Ethernet connection:
  if (Ethernet.begin(mac) == 0) {
    //Serial.println("Failed using DHCP");
    // no point in carrying on, so do nothing forevermore:
    for(;;);
  }
  gateway_id        =   getRomValue("id");
  gateway_password  =   getRomValue("pwd");
  // give the Ethernet shield a second to initialize:
  delay(1000);
  //Serial.println(Ethernet.localIP());
  // //Serial.println("connecting...");
// use 7, 8 pin as the ce and cs for radio. and read the addr from EEPROM (id)
  radio.begin(7, 8, gateway_id);
  // try to conenct, until connected:
  while(!client->connect(server, port)){
    // //Serial.println("connection falied, retrying 3s later...");
    delay(3000);
  }
  // if you get a connection, report back via serial:
  //Serial.println("connected");

  // call the connect event
  onEthernetConnect(client);
}

unsigned long last_heartbeat_time = 0;
unsigned long disconnect_retry_time = 0;
void loop(){
  // if radio get something: 
  String read_radio = radio.readRadio();
  if(read_radio != ""){
    onRadioMessage(radio, read_radio);
  }
  // if there are incoming bytes available
  // from the server, read them and print them:
  if(client->connected()){
    String read = tryToReadEthernet(client);
    if(read != ""){
      // judge if heartbeat
      if(getHead(read) == "ping"){
        // //Serial.println("\nget ping. Responding.");
        sendToServer(client, "", "pong");
        // sendToServer(client,"","getDevices");
      }
      else onEthernetMessage(client, read);
      last_heartbeat_time = millis();
    }
  }

  
  // check if disconnected
  if( (millis() - last_heartbeat_time) > no_heartbeat_limit_time){
    if( ((millis() - disconnect_retry_time) > 3000)){
        if(!client->connect(server, port)){
              //Serial.println();
              //Serial.println("disconnecting.");
              client->stop();
              // delete client;
              // client = new EthernetClient();
              // //Serial.println("connection falied, retrying 3s later...");
              disconnect_retry_time = millis();
        }
    }
    if( client->connected()){
          last_heartbeat_time = millis();
          // if get a connection, report back via serial:
          //Serial.println("connected");
          delay(500);
          // call the connect event
          onEthernetConnect(client);
      }
  }

}

// return empty string "", if get nothing or not available;
String tryToReadEthernet(EthernetClient *client){
  if (client->available()) {
  // //Serial.println("from server:");
    char tmp;
    String data = "";
    String receive = "";
    while(client->available()){
      tmp = client->read();
      receive += tmp;
      // the protocol, end at '\n'
      if(tmp == '\0'){
        data += decode(receive);
        // //Serial.println(receive);
        receive = "";
      }else if(tmp == '\n') break;
    }
    return data;
  }
  return "";
}

// send to worker server
void sendToServer(EthernetClient *client, String data, String type){
  if(!client->connected()&&type!="login"){
    //Serial.println("NotConnect");
    return;
  }
  String to_send = "{"
                   "\"agent\":\"node\","
                   "\"type\":\""+type+"\","
                   "\"values\":\""+data+"\""
                   "}";
  // //Serial.println("Sending: "+to_send);
  // String to_send_encoded = encode(to_send);
  // //Serial.println(to_send_encoded.length());
  // //Serial.println("test");
  int count = (to_send.length() % 10)==0 ? to_send.length() / 10 : to_send.length() / 10 + 1;
  for(int i =0; i < count; i++){
    String data = encode(to_send.substring(i*10,(i+1)*10));
    // //Serial.println(data);
    client->print(data+'\0');
  }
  // for(int i = 0; i<to_send_encoded.length();i++){
  //   client->print(to_send_encoded[i]);
  //   // //Serial.print(to_send_encoded[i]);
  // }
  client->print("\n");
}

// if there are some messages available.
void onEthernetMessage(EthernetClient *client, String &data){

  String head = getHead(data);
  
  // start judging the head:
  if(head == "logined"){
    // //Serial.println("login succeed");
  }
  else if(head == "not login"){
    // login
    // //Serial.println("\nnot login.");
    // //Serial.println("trying to login");
    sendToServer(client, "gateway_id:"+gateway_id+"\\npassword:"+gateway_password, "login");
    // sendToServer(client, "gateway_id:gatew\\npassword:gatew123\\n", "login");
  }
  else if(head == "to node"){
    String node_id = getValue(data,"node_id");
    String node_value = getValue(data,"node_value");
    node_value += "\ngateway_id:"+gateway_id;
    //Serial.println(node_id+"\n"+node_value); 
    radio.sendRadio(node_id, node_value);
  }
  // else if(head == "device"){
  //   // //Serial.println("The DeviceList:");
  //   //Serial.println(data);
  //   String node_id = data.substring(data.indexOf('\n')+1);
  //   if(radio.sendRadio(node_id, "ping\ngateway_id:"+gateway_id) == false){ // if cannot connect the node, inform the server.
  //      sendToServer(client, "node_id:"+node_id, "loseNode");
  //    }
  //    else{
  //       sendToServer(client,"node_id:"+node_id,"onlineNode");
  //    }
  //   // for(int i = data.indexOf('\n')+1; i != 0; i++){
  //   //   int to = data.indexOf('\n', i);
  //   //   String node_id = data.substring(i, to);
  //   //   i = to;
  //   //   //Serial.println("\""+node_id+"\"");
  //   //   if(radio.sendRadio(node_id, "ping\ngateway_id:"+gateway_id) == false){ // if cannot connect the node, inform the server.
  //   //     String data = "node_id:" + node_id;
  //   //     sendToServer(client, data, "loseNode");
  //   //   };
  //   // }
  //   // //Serial.println("=========End=========");
  // }
    /*
   else if(){

   }
  */
  else{
    //Serial.println("\ncan't read:\"" + data + "\"");
    // //Serial.println(getValue(data,"node_id")+"\n"+getValue(data,"node_value"));
  }

}

// after conenct the server
void onEthernetConnect(EthernetClient *client){
  // login
  // //Serial.println("trying to login");
  // 如果不能登录的话，请尝试使用下面的这句"pong"，并且注释掉登录
  // sendToServer(client, "", "pong");
  sendToServer(client, "gateway_id:"+gateway_id+"\\npassword:"+gateway_password+"\\n", "login");
  // delay(10);
  sendToServer(client,"", "getDevices");
}

// when radio gets something:
void onRadioMessage(AiromeRadio &radio, String &data){
  // //Serial.println("===radio===");
  //Serial.println(data);
  if(getHead(data) == "toServer"){
    sendToServer(client,getValue(data,"values"),"values");
  }
  // //Serial.println("==========");
}

// get value from eeprom
String getRomValue(const char* key){
  String rom_content = "";
  for(int i = 0; EEPROM[i] != 0 && i < EEPROM.length(); i++){
    rom_content += (char)EEPROM[i];
  }
  //Serial.println("EEPROM:"+rom_content);
  return getValue(rom_content, key);
}

// get value with key from the data
String getValue(const String &data, const char* key){
  int index = data.indexOf(key);
  if(index == -1){
    return "";
  }
  int from = index + strlen(key) + 1; //key.length() + 1;
  int to = 0;
  if( (to = data.indexOf('\n', index)) == -1){
    return data.substring(from);
  }else
    return data.substring(from, to);
}

// get head
String getHead(String& data){
  String head = "";
  if(data.indexOf('\n')==-1){
    head = data;
  }else{
    head = data.substring(0,data.indexOf('\n'));
  }
  return head;
}