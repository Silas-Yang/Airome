#ifndef __Gree_h__
#define __Gree_h__

#include <Arduino.h>
#include "IRremote.h"

//#define DEBUG 1

class GreeAC
{
  private:
    void sendpresumable();
    void send0();
    void send1();
    void sendGree(byte ircode, byte len);
  public:
    void setstate(byte mode, byte fan, byte temp, byte power);
    void sendIR(int stat,int temperature);
};

extern IRsend irsend;

#endif
