#include "AiromeRadio.h"
void AiromeRadio::begin(int ce, int cs,  String addr){
	radio = new RF24(ce,cs);
	radio->begin();
	for(int i=0; i<5; i++){
		address[i] = addr[i];
	}
	radio->openReadingPipe(1, address);
	radio->startListening();
}

AiromeRadio::~AiromeRadio(){
	delete radio;
}

bool AiromeRadio::sendRadio(String to_address, String data){
	// Serial.println("sendRaio");
	byte to_addr[6];
	for(int i = 0; i < 6; i++){
		to_addr[i] = to_address[i];
	}
	radio->stopListening();
	radio->openWritingPipe(to_addr);
	unsigned long time_limit = millis();
	String to_send = "";
	int count = (data.length() % 10)==0 ? data.length() / 10 : data.length() / 10 + 1;
	Serial.println(count);
	for(int i =0; i < count; i++){
		String ecrypt =  encode(data.substring(i*10,(i+1)*10));
		to_send += ecrypt;
		// Serial.println(ecrypt);
		to_send += '\0';
	}
	to_send += '\n';
	// Serial.println("the data to send is :\""+to_send+"\"");

	for(int i = 0; i < to_send.length(); i++){
		while( radio->write( &to_send[i], sizeof(char) )!=1 ){
			// Serial.print(radio->write( &to_send[i], sizeof(char) ));
			// Serial.println(to_send[i]);
			// Serial.println("failed");
			if(millis() - time_limit > 500){
				// Serial.println("time out, the following data is not sent: ");
				// for( int j = i; j<to_send.length(); j++){
				// 	Serial.print(to_send[j]);
				// }
				// Serial.println("");
				radio->startListening();
				return false;
			}
		};
		
	 }
	radio->startListening();
	return true;
}

String AiromeRadio::readRadio(){
	String data = "";
	if(radio->available()){
		// Serial.println("On radio");
		char tmp;
		String receive = "";
		unsigned long start_reading = millis();
		unsigned long time_limit = 2000;
		while(millis() - start_reading < time_limit){
			if(radio->available()){
				radio->read(&tmp, sizeof(char));
				receive += (char)tmp;
				if(tmp == '\0'){
					data += decode(receive);
					Serial.println(receive);
					receive = "";
				}else if(tmp == '\n') {
					return data;
				};
				// Serial.print(tmp);
				// if(tmp == '\n')  {
				// 	// Serial.print(data);
				// 	return decode(data);
				// }
			}
		}
		// Serial.println("reading timeout");
	}
	return "";
}
