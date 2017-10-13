#include "AiromeRadio.h";
#include <Base64.h>
#include <RF24.h>
#include <SPI.h>
#include <EEPROM.h>
#include <IRremote.h>
#include "Gree.h"
AiromeRadio radio;
String gateway_id;
String node_id;
String node_password;

IRsend irsend;
GreeAC gree;
const int IR_PIN = 3; // cannot change this pin.
void setup(){
	Serial.begin(9600);
     delay(100);
     node_id = getRomValue("id");
	radio.begin(7, 8, getRomValue("id"));
	Serial.println(getRomValue("id"));
     pinMode(IR_PIN, OUTPUT);
     digitalWrite(IR_PIN, LOW);
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
void onRadioMessage(String &data){
      Serial.println("======read======");
      Serial.println(data);
      Serial.println("================");
      String head = getHead(data);

      // if(head == "ping"){
      //   gateway_id = getValue(data, "gateway_id");
      //   String values = "type:pong\\nnode_id:"+node_id+"\\n";
      //   radio.sendRadio(gateway_id, "toServer\nvalues:"+values);
      //   Serial.println(gateway_id);
      // }
      // else{
          String air_switch = head.substring(0,head.indexOf(','));
          String temperature = head.substring(head.indexOf(',')+1);
          Serial.println("the switch: "+air_switch);
          Serial.println("the temperature: "+temperature);
          delay(100);
          gree.sendIR(air_switch.toInt(), temperature.toInt());
      // }
}

void nodeEvent(){
  // if(millis() - time > 10000){   // this is a loop, please set the time.
  //   time = millis();
  //   // TO DO:
  // }
}