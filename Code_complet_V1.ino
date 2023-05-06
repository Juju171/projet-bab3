#include <DFRobot_ID809.h>

#define COLLECT_NUMBER 3  //Fingerprint sampling times, can be set to 2-3

/*Use software serial when using UNO or NANO */
#if ((defined ARDUINO_AVR_NANO) || (defined ARDUINO_AVR_UNO))
    #include <SoftwareSerial.h>
    SoftwareSerial Serial1(2, 3);  //RX, TX
    #define FPSerial Serial1
/*Use software serial when using ESP8266 */
#elif((defined ESP8266))
    #include <SoftwareSerial.h>
    SoftwareSerial Serial2(12, 14);  //RX, TX
    #define FPSerial Serial2
#else
    #define FPSerial Serial2
#endif

DFRobot_ID809 fingerprint;

void setup(){
  /*Init print serial port */
  Serial.begin(9600);
  /*Init FPSerial*/
  FPSerial.begin(9600);
  /*Take FPSerial as communication port of the module*/
  fingerprint.begin(FPSerial);
  /*Wait for Serial to open*/
  while(!Serial);
  /*Test whether the device can communicate properly with mainboard 
    Return true or false
    */
  while(fingerprint.isConnected() == false){
    Serial.println("Communication with device failed, please check connection");
    /*Get error code information*/
    //desc = fingerprint.getErrorDescription();
    //Serial.println(desc);
    delay(1000);
  }
  Serial.println("Test 1");
}

uint8_t ID,i,ret;

void menu(int& choix){
    Serial.println("Que voulez-vous faire : \n1.Ajouter une empreinte digitale\n2.Tester l'empreinte\n3.Supprimer une empreinte\n4.Supprimer toutes les empreintes\n5.Quitter le programme\n\n");
    Serial.println("Votre choix : ");

    while(Serial.available() == 0) { }  // Wait for an input
    choix = Serial.parseInt();
    
    while(choix<0||choix>5){
      Serial.println("Veuillez entrer un choix parmis ceux possibles.\nVotre choix : ");
      while(Serial.available() == 0) { }  // Wait for an input
      choix = Serial.parseInt();
    }
}


void add_finger(){
  
  if((ID = fingerprint.getEmptyID()) == ERR_ID809){
    while(1){
      /*Get error code information*/
      //desc = fingerprint.getErrorDescription();
      //Serial.println(desc);
      delay(1000);
    }
  }
  Serial.print("unregistered ID,ID=");
  Serial.println(ID);
  i = 0;   //Clear sampling times 
  /*Fingerprint sampling 3 times*/
  while(i < COLLECT_NUMBER){
    /*Set fingerprint LED ring mode, color, and number of blinks 
      Can be set as follows: 
      Parameter 1:<LEDMode>
      eBreathing   eFastBlink   eKeepsOn    eNormalClose
      eFadeIn      eFadeOut     eSlowBlink   
      Parameter 2:<LEDColor>
      eLEDGreen  eLEDRed      eLEDYellow   eLEDBlue
      eLEDCyan   eLEDMagenta  eLEDWhite
      Parameter 3:<Number of blinks> 0 represents blinking all the time 
      This parameter will only be valid in mode eBreathing, eFastBlink, eSlowBlink
     */
    fingerprint.ctrlLED(/*LEDMode = */fingerprint.eBreathing, /*LEDColor = */fingerprint.eLEDMagenta, /*blinkCount = */0);
    Serial.print("The fingerprint sampling of the fingerprint n");
    Serial.print(i+1);
    Serial.println("(fn) is being taken");
    Serial.println("Please press down your finger");
    /*Capture fingerprint image, 10s idle timeout, if timeout=0,Disable  the collection timeout function
      IF succeeded, return 0, otherwise, return ERR_ID809
     */
    if((fingerprint.collectionFingerprint(/*timeout =*/10)) != ERR_ID809){
      /*Set fingerprint LED ring to quick blink in yellow 3 times */
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eFastBlink, /*LEDColor = */fingerprint.eLEDYellow, /*blinkCount = */3);
      Serial.println("Sampling succeeds");
      i++;   //Sampling times +1
    }else{
      Serial.println("Sampling failed");
      /*Get error code information*/
      //desc = fingerprint.getErrorDescription();
      //Serial.println(desc);
    }
    Serial.println("Please release your finger");
    /*Wait for finger to release 
      Return 1 when finger is detected, otherwise return 0 
     */
    while(fingerprint.detectFinger());
  }
}


void match_finger(){

  uint8_t ret = 0;
  /*Set fingerprint LED ring mode, color, and number of blinks
    Can be set as follows:
    Parameter 1:<LEDMode>
    eBreathing   eFastBlink   eKeepsOn    eNormalClose
    eFadeIn      eFadeOut     eSlowBlink   
    Parameter 2:<LEDColor>
    eLEDGreen  eLEDRed      eLEDYellow   eLEDBlue
    eLEDCyan   eLEDMagenta  eLEDWhite
    Parameter 3:<number of blinks> 0 represents blinking all the time
    This parameter will only be valid in mode eBreathing, eFastBlink, eSlowBlink
   */
  fingerprint.ctrlLED(/*LEDMode = */fingerprint.eBreathing, /*LEDColor = */fingerprint.eLEDBlue, /*blinkCount = */0);
  Serial.println("Please press down your finger");
  /*Capture fingerprint image, Disable the collection timeout function 
    If succeed return 0, otherwise return ERR_ID809
   */
  if((fingerprint.collectionFingerprint(/*timeout=*/0)) != ERR_ID809){
    /*Set fingerprint LED ring to quick blink in yellow 3 times*/
    fingerprint.ctrlLED(/*LEDMode = */fingerprint.eFastBlink, /*LEDColor = */fingerprint.eLEDYellow, /*blinkCount = */3);
    Serial.println("Capturing succeeds");
      Serial.println("Please release your finger");
    /*Wait for finger to release
      Return 1 when finger is detected, otherwise return 0 */
    while(fingerprint.detectFinger());

    /*Compare the captured fingerprint with all the fingerprints in the fingerprint library 
      Return fingerprint ID(1-80) if succeed, return 0 when failed */
    ret = fingerprint.search();
    /*Compare the captured fingerprint with a fingerprint of specific ID 
      Return fingerprint ID(1-80) if succeed, return 0 when failed */
    //ret = fingerprint.verify(/*Fingerprint ID = */1);  
    if(ret != 0){
      /*Set fingerprint LED ring to always ON in green */
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDGreen, /*blinkCount = */0);
      Serial.print("Matching succeeds,ID=");
      Serial.println(ret);
    }else{
      /*Set fingerprint LED ring to always ON in red*/
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDRed, /*blinkCount = */0);
      Serial.println("Matching fails");
    }
  }else{
    Serial.println("Capturing fails");
    /*Get error code information*/
    //desc = fingerprint.getErrorDescription();
    //Serial.println(desc);
  }
  Serial.println("-----------------------------");
  delay(1000);

  /*Save fingerprint in an unregistered ID */
  if(fingerprint.storeFingerprint(/*Empty ID = */ID) != ERR_ID809){
    Serial.print("Saving succeed，ID=");
    Serial.println(ID);
    Serial.println("-----------------------------");
    /*Set fingerprint LED ring to always ON in green */
    fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDGreen, /*blinkCount = */0);
    delay(1000);
    /*Turn off fingerprint LED ring */
    fingerprint.ctrlLED(/*LEDMode = */fingerprint.eNormalClose, /*LEDColor = */fingerprint.eLEDBlue, /*blinkCount = */0);
    delay(1000);
  }else{
    Serial.println("Saving failed");
    /*Get error code information*/
    //desc = fingerprint.getErrorDescription();
    //Serial.println(desc);
  }
  
}


void delete_finger(){
  uint8_t ret = 0;
  Serial.println("Press your finger on the sensor to delete the fingerprint");
  /*Set fingerprint LED ring mode, color, and number of blinks 
    Can be set as follows: 
    Parameter 1:<LEDMode>
    eBreathing   eFastBlink   eKeepsOn    eNormalClose
    eFadeIn      eFadeOut     eSlowBlink   
    Parameter 2:<LEDColor>
    eLEDGreen  eLEDRed      eLEDYellow   eLEDBlue
    eLEDCyan   eLEDMagenta  eLEDWhite
    Parameter 3:<Number of blinks> 0 represents blinking all the time 
    This parameter will only be valid in mode eBreathing, eFastBlink, eSlowBlink */
  fingerprint.ctrlLED(/*LEDMode = */fingerprint.eBreathing, /*LEDColor = */fingerprint.eLEDBlue, /*blinkCount = */0);
  /*Capture fingerprint image, 10s idle timeout, if timeout=0,Disable  the collection timeout function
    If succeed return 0, otherwise return ERR_ID809 */
  if((fingerprint.collectionFingerprint(/*timeout=*/10)) != ERR_ID809){
    /*Compare the captured fingerprint with all the fingerprints in the fingerprint library 
      Return fingerprint ID(1-80) if succeed, return 0 when failed */
    ret = fingerprint.search();
    if(ret != 0){
      /*Delete the fingerprint of this ID*/
      fingerprint.delFingerprint(/*Fingerprint ID = */ret);
      //fingerprint.delFingerprint(DELALL);  //Delete all fingerprints 
      Serial.print("delete succeeds,ID=");
      Serial.println(ret);
      /*Set fingerprint LED ring to always ON in green */
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDGreen, /*blinkCount = */0);
    }else{
      Serial.println("Fingerprint is unregistered");
      /*Set fingerprint LED ring to always ON in red*/
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDRed, /*blinkCount = */0);
    }
  }else{
    Serial.println("Capturing fails");
    /*Get error code information*/
    //desc = fingerprint.getErrorDescription();
    //Serial.println(desc);
    /*Set fingerprint LED ring to always ON in red*/
    fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDRed, /*blinkCount = */0);
  }
  Serial.println("Please release your finger");
  /*Wait for finger to release
    Return 1 when finger is detected, otherwise return 0 */
  while(fingerprint.detectFinger());
    /*Check whether the fingerprint ID has been registered 
    Return 1 if registered, otherwise return 0 */
//  if(fingerprint.getStatusID(/*Fingerprint ID = */ret)){
//    Serial.println("ID has been registered");
//  }else{
//    Serial.println("ID is unregistered");
//  }
  Serial.println("-----------------------------");
  delay(1000);
  
}


void delete_all(){
  uint8_t ret = 0;
  
  for(int i=0;i<80;i++){
      fingerprint.delFingerprint(i);
      Serial.print("delete succeeds, ID = ");
      Serial.println(i);
     }
  fingerprint.ctrlLED(fingerprint.eFastBlink,fingerprint.eLEDRed,5);
  
  Serial.println("-----------------------------");
  delay(1000);
}


void loop(){
  fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDWhite,0);
  int choix;
  do{
    menu(choix);
    switch(choix){
      case 1:
      add_finger();
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDWhite,0);
      break;
      case 2:
      match_finger();
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDWhite,0);
      break;
      case 3:
      delete_finger();
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDWhite,0);
      break;
      case 4:
      delete_all();
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDWhite,0);
      break;
    }
  }while(choix!=5);
  for(;;){
    //Mode veille
  }
}
