#ifndef __AIROMECLIENT_H__
#define __AIROMECLIENT_H__
#include <Arduino.h>
#include <SPI.h>
#include <Ethernet.h>

class AiromeClient{
public:
	//construct
	AiromeClient();
        //begin
        void begin();
	//return the status code
	int post(char* host_name,char* path, String data, String& response);
private:
	EthernetClient client;
};

#endif
