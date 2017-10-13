#include "AiromeRadio.h";
#include <Base64.h>
#include <RF24.h>
#include <SPI.h>
#include <EEPROM.h>
#include "dht11.h"
AiromeRadio radio;
int Pin = 3; // the pin to control light in this example
String gateway_id;
String node_id;
String node_password;
dht11 dht;
unsigned long time=0;
void setup(){
  Serial.begin(9600);
  delay(100);
  node_id = getRomValue("id");
  radio.begin(7, 8, node_id);
  Serial.println(node_id);
}
String read = "";
void loop(){
  if((read = radio.readRadio()) != ""){
        onRadioMessage(read);
  }
  nodeEvent();
    // radio.sendRadio("gatew", "test");
    // delay(1000);
  // radio.sendRadio("gatew", "test");
  // Serial.println("sending");
}

// get value from eeprom
String getRomValue(String key){
  String rom_content = "";
  for(int i = 0; EEPROM[i] != 0 && i < EEPROM.length(); i++){
    rom_content += (char)EEPROM[i];
  }
  // Serial.println("EEPROM:"+rom_content);
  return getValue(rom_content, key);
}

// get value with key from the data
String getValue(const String &data, String key){
  int index = data.indexOf(key);
  if(index == -1){
    return "";
  }
  int from = index + key.length() + 1;
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

// receiving the data from radio.
void onRadioMessage(String data){
      Serial.println("======read======");
      Serial.println(data);
      Serial.println("================");
      String head = getHead(data);
        gateway_id = getValue(data, "gateway_id");
      if(head == "ping"){
        String values = "type:pong\\nnode_id:"+node_id+"\\n";
        radio.sendRadio(gateway_id, "toServer\nvalues:"+values);
        Serial.println(gateway_id);
      }
      else if(head=="getData"){
        delay(10);
        dht.read(3);
        String data = "toServer\n";
        data += "values:";
        String values = "node_id:"+node_id+"\\n"+"type:value\\n"+"content:"+(dht.temperature-2)+","+dht.humidity;
        data += values;
        radio.sendRadio(gateway_id, data);
      }
}

void nodeEvent(){
  // if(millis() - time > 10000){
  //   time = millis();
  //   dht.read(3);
  //   String data = "toServer\n";
  //   data += "values:";
  //   String values = "node_id:"+node_id+"\\n"+"type:value\\n"+"content:"+(dht.temperature-2)+","+dht.humidity;
  //   data += values;
  //   radio.sendRadio(gateway_id, data);
  //   Serial.println(data);
  // }
}
