#include "AiromeRadio.h";
#include <Base64.h>
#include <RF24.h>
#include <SPI.h>
#include <EEPROM.h>
AiromeRadio radio;
String gateway_id;
String node_id;
String node_password;
int LED = 3; // the pin to control light in this example
void setup(){
	Serial.begin(9600);
     delay(100);
     node_id = getRomValue("id");
	radio.begin(7, 8, getRomValue("id"));
	Serial.println(getRomValue("id"));
    pinMode(LED, OUTPUT);
    digitalWrite(LED,LOW);
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
      if(head == "1"){
          Serial.println("on");
          digitalWrite(LED,HIGH);
      }else if(head == "0"){
          Serial.println("off");
          digitalWrite(LED, LOW);
      }
      else if(head == "ping"){
        gateway_id = getValue(data, "gateway_id");
        String values = "type:pong\\nnode_id:"+node_id+"\\n";
        radio.sendRadio(gateway_id, "toServer\nvalues:"+values);
        Serial.println(gateway_id);
      }
}

void nodeEvent(){
  // if(millis() - time > 10000){
  //   time = millis();
  //   // TO DO:
  // }
}