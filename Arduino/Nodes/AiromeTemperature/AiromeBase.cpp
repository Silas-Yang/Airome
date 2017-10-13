#include <Arduino.h>
#include <Base64.h>
String encode(String str){
  char *input = new char[str.length()];
  for(int i = 0; i < str.length(); i++){
    input[i] = str[i];
  };
  int enc_length  =   base64_enc_len(str.length());
  char *result    =   new char[enc_length];
  base64_encode(result,input,str.length());
  String res_enc = result;
  delete[] result;
  delete[] input;
  return res_enc;
}

// base64 decode
String decode(String str){
  char *input = new char[str.length()];
  for(int i = 0; i < str.length(); i++){
    input[i] = str[i];
  };
  int dec_length  =   base64_dec_len(input,str.length());
  char *result    =   new char[dec_length];
  base64_decode(result,input,str.length());
  String res_dec = result;
  delete[] result;
  delete[] input;
  return res_dec;
}