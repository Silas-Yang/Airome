#include "Gree.h"
#include <math.h> 

void GreeAC::sendpresumable()
{
	irsend.mark ( 9000 );
	irsend.space( 4500 );
}

void GreeAC::send0( )
{
  irsend. mark ( 560 );
  irsend.space( 565 );
}

void GreeAC::send1()
{
  irsend.mark ( 560 );
  irsend.space( 1690 );
}

void GreeAC::sendGree(byte ircode, byte len)
{
    byte mask = 0x01;
    for(int i = 0;i < len;i++)
    {
        if (ircode & mask)  send1();
        else send0();
	mask <<= 1;
    }
}

void GreeAC::sendIR(int stat, int temp)
{
//    int i,j;
//    int a[10];
//    byte mask = 0x00,tmp = 0x00;
//    
//    for (i = 0; i <4; i++)
//    {
//	a[i] = (1 & n);
//	n >>= 1;
//       
       // mask += (byte)( a[i]*pow( (double)2, 7-i ) ) ;  
       
//        j = 7-i;
//        tmp = 1;
//        if( a[i] == 1 ){
//          while( j-- )
//            tmp <<= 1;
//	  mask += tmp ;
//        }
        
       // Serial.print("Mask = ");
       // Serial.println( mask,HEX );
//    }
    
//    for(i=0;i<4;i++)
//      Serial.print(a[i]);
//      
//    Serial.println("");
    //Serial.println(mask);
    //Serial.println(mask,HEX);
    int s=0;
    if(stat==1) s=73;
    else if(stat==0) s=65;
    irsend.enableIROut(38);
    sendpresumable();
    //sendGree( 73, 8); //turn on
    //sendGree( 65, 8 );//turn off
    sendGree ( s, 8);
    sendGree(temp-10, 8);
    //sendGree( 0x0A, 8 ); temperature:26;
    sendGree( 0x20, 8 );
    sendGree( 0x50, 8 );
    sendGree( 0x02, 3 );
    irsend. mark( 560 );
    irsend.space( 10000 );
    irsend.space( 10000 );
    sendGree( 0x00, 8 );
    sendGree( 0x21, 8 );
    sendGree( 0x00, 8 );
    sendGree( 0xF0, 8 );
    irsend.mark( 560 );
    irsend.space( 0 );
}
