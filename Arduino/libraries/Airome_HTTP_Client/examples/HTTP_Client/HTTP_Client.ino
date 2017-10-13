#include "AiromeClient.h"
#include <SPI.h>
#include <Ethernet.h>
  AiromeClient client;
void setup(){
  Serial.begin(9600);
  client.begin();
  
}

void loop(){
  String str;
  Serial.println("starting");
  client.post("airome.duapp.com", "/status.php", "user=ylh&pwd=123", str);
  
  Serial.println("Finished");
  delay(500);
}
