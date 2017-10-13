#include <Arduino.h>
#include <EEPROM.h>

void setup(){
  Serial.begin(9600);
  Serial.println(
    "welcome to AiromeCLI.\n"
    "eeprom:\n"
    "\tshow\n"
    "\tclear\n"
    "setid newid\n"
    "setpwd newpwd\n"
    );
}

void loop(){
    // read command line from Serial
  if(Serial.available()){
    String command = "";
    for(char tmp = 0;tmp!='\n';){
      if(Serial.available()){
        tmp = Serial.read();
        command += tmp;
      }
    }
    Serial.print("\nexecuting: "+command);
    int index = 0;
    command.trim();
    for(int i = 0; i < command.length(); i++){
      if(command[i] == ' ') index++;
    }
    String* avg = new String[index+1];
    int split = -1;
    for(int i = 0; (split = command.indexOf(' ')) != -1; i++){
      avg[i] = command.substring(0,split);
      command = command.substring(split+1);
    }
    avg[index] = command;
    // Serial.print("index:");
    // Serial.println(index);
    // Serial.print("the avg are:\n");
    // for(int i = 0; i<=index; i++){
    //   Serial.println(avg[i]);
    // }
    Serial.println("========Result=========");
    if(avg[0] == "eeprom"){
      eeprom(avg,index);
    }
    ///////setid///////
    else if(avg[0] == "setid"){
      setid(avg, index);
    }
    ///////setpwd/////
    else if(avg[0] == "setpwd"){
      setpwd(avg, index);
    }
    else{
      Serial.println("unknown command.");
    }
    Serial.println("=======================");
    delete[] avg;
    while(!Serial.available()); // waiting for the next command.
  }
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

//// command eeprom
void eeprom(String* avg, int index){
  if(index < 1){
    Serial.println("need parameter(s)");
    return;
  }
  if(avg[1] == "show"){
    for(int i = 0; i<EEPROM.length(); i++){
      if(EEPROM[i] == 0){
        break;
      }
      Serial.print((char)EEPROM[i]);
    }
  }
  else if(avg[1] == "clear"){
    for(int i=0; i<EEPROM.length(); i++){
      EEPROM[i] = 0;
    }
  }
}

void setid(String* avg, int index){
  String EEPROM_content = "";
  for(int i = 0; i<EEPROM.length(); i++){
    if(EEPROM[i] == 0){
      break;
    }
    EEPROM_content += (char)EEPROM[i];
  }
  Serial.println("The old id:"+getValue(EEPROM_content,"id"));
  String value = getValue(EEPROM_content,"id");
  if( value != ""){
    EEPROM_content.replace("id:"+value, "id:"+avg[1]);
  }else{
    EEPROM_content += "id:" + avg[1] + '\n';
  }
  for(int i = 0; i < EEPROM_content.length() && i < EEPROM.length(); i++){
    EEPROM[i] = EEPROM_content[i];
  }
  Serial.println("The new id:"+getValue(EEPROM_content,"id"));
}

void setpwd(String* avg, int index){
  String EEPROM_content = "";
  for(int i = 0; i<EEPROM.length(); i++){
    if(EEPROM[i] == 0){
      break;
    }
    EEPROM_content += (char)EEPROM[i];
  }
  Serial.println("The old pwd:"+getValue(EEPROM_content,"pwd"));
  String value = getValue(EEPROM_content,"pwd");
  if( value != ""){
    EEPROM_content.replace("pwd:"+value, "pwd:"+avg[1]);
  }else{
    EEPROM_content += "pwd:" + avg[1] + '\n';
  }
  for(int i = 0; i < EEPROM_content.length() && i < EEPROM.length(); i++){
    EEPROM[i] = EEPROM_content[i];
  }
  Serial.println("The new pwd:"+getValue(EEPROM_content,"pwd"));
}
