#ifndef __AIROMERADIO_H__
#define __AIROMERADIO_H__
#include <Arduino.h>
#include "AiromeRadio.h"
#include <Base64.h>
#include "AiromeBase64.h"
#include <RF24.h>
#include <SPI.h>
class AiromeRadio{
private:
	RF24* radio;
	byte address[5];
public:
	void begin(int ce, int cs,  String addr);
	~AiromeRadio();
	bool sendRadio(String to_address, String data);
	String readRadio();
};
#endif