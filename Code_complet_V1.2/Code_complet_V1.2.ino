#include <DFRobot_ID809.h>
#include <ESP8266WiFi.h> 
#include <ESP8266WebServer.h>
#include <LiquidCrystal_I2C.h>
#include <Servo.h>

// Crée un serveur sur le port 80
ESP8266WebServer server(80);  

//Fingerprint sampling times, can be set to 2-3
#define COLLECT_NUMBER 2  

// Définir le SSID et le mot du passe du wifi que le module wifi va générer en mode access point
const char* ssid = "ESP8266";
const char* password = "123456";

// Créez un objet de type Servo pour contrôler le servomoteur
Servo myservo;  

// Assignez la broche D3 au contrôle du bouton
const int boutonPin = D3;

// Variables pour stocker l'état précédent du bouton et l'état actuel
int boutonState;
int lastButtonState = LOW;

// Initialisez les variables que nous allons utiliser plus tard
int fonction = 9;
uint8_t ID,i=0;

// Initialisez l'objet LiquidCrystal_I2C avec l'adresse I2C et les dimensions de l'écran LCD
LiquidCrystal_I2C lcd(0x27, 16, 2); // Adresse I2C 0x3F, écran LCD 16x2

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
  /*Test whether the device can communicate properly with mainboard return true or false*/

  while(fingerprint.isConnected() == false){
    Serial.println("Communication with device failed, please check connection");
    /*Get error code information*/
    //desc = fingerprint.getErrorDescription();
    //Serial.println(desc);
    delay(1000);
  }

// Initialisation de la connexion WiFi
WiFi.softAP(ssid, password);
Serial.println("Access Point créé !");
Serial.print("Connect to ESP8266AP with password : ");
Serial.print(password);
IPAddress myIP = WiFi.softAPIP();
Serial.print("AP IP address: ");
Serial.println(myIP);
server.on("/", handleRoot);  

// Démarre le serveur
server.begin();  

// Initialisez la broche boutonPin comme une entrée
pinMode(boutonPin, INPUT);

// Activez les interruptions pour la broche D3
attachInterrupt(digitalPinToInterrupt(boutonPin), detecterChangementBouton, CHANGE);

// Initialisez l'écran LCD
lcd.init();

// Allumez l'écran LCD et activez le rétroéclairage
lcd.backlight();
lcd.clear();

lcd.setCursor(0,0);
lcd.print("Test");

// Attachez le servomoteur à la broche 4
myservo.attach(4);  

// Définissez l'angle du servomoteur à 0 degrés
myservo.write(0);
// Atendre une seconde
delay(1000); 

}

void handleRoot() {
  if (server.method() == HTTP_POST) {
    String payload = server.arg("plain");
    payload.trim();
    int index = payload.indexOf('&');
    if (index > 0 && index < payload.length() - 1) {
      String fonctionStr = payload.substring(0, index);
      String IDStr = payload.substring(index + 1);
      fonction = fonctionStr.toInt();
      ID = IDStr.toInt();
    }
    server.send(200, "text/plain", "OK");
  }
}

// Fonction pour détecter le changement d'état du bouton
void detecterChangementBouton() {
  // Ne faites rien dans cette fonction, car tout est géré dans la boucle loop()
}


void menu(int& choix){
    Serial.println("\n\nQue voulez-vous faire : \n1.Ajouter une empreinte digitale\n2.Tester l'empreinte\n3.Supprimer une empreinte\n4.Supprimer toutes les empreintes\n5.Quitter le programme\n");
    Serial.println("Votre choix : ");

    while(Serial.available() == 0) { }  // Wait for an input
    choix = Serial.parseInt();
    
    while(choix<0||choix>5){
      Serial.println("Veuillez entrer un choix parmis ceux possibles.\nVotre choix : ");
      while(Serial.available() == 0) { }  // Wait for an input
      choix = Serial.parseInt();
    }
}

void add_finger(uint8_t ID){ //1
  
  /*if((ID = fingerprint.getEmptyID()) == ERR_ID809){
    while(1){
      /*Get error code information*/
      /*desc = fingerprint.getErrorDescription();
      /*Serial.println(desc);
      delay(1000);
    }
  }*/
  
  Serial.print("ID=");
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
    Serial.println(" is being taken");
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
    Serial.println("-----------------------------");
    /*Get error code information*/
    //desc = fingerprint.getErrorDescription();
    //Serial.println(desc);
  }
}

void match_finger(){ //2

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

      // Effacez l'écran LCD
      lcd.clear();

      // Affichez le message sur l'écran LCD
      lcd.setCursor(0, 0);
      lcd.print("Ouverture...");
      lcd.setCursor(0,1);
      lcd.print("ID : ");
      lcd.setCursor(6,1);
      lcd.print("12"); //A CHANGER 
      delay(2500); // Attendre 2.5s
      lcd.clear();

      myservo.write(90);  // Définissez l'angle du servomoteur à 90 degrés
      delay(15000);        // Attendez 15s
      myservo.write(0);   // Définissez l'angle du servomoteur à 0 degrés
      delay(1000);        // Attendez une seconde

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
}

void delete_finger(uint8_t ID){ //3
      fingerprint.delFingerprint(/*Fingerprint ID = */ID);
      //fingerprint.delFingerprint(DELALL);  //Delete all fingerprints 
      Serial.print("delete succeeds,ID=");
      Serial.println(ID);
      /*Set fingerprint LED ring to always ON in green */
      fingerprint.ctrlLED(/*LEDMode = */fingerprint.eKeepsOn, /*LEDColor = */fingerprint.eLEDGreen, /*blinkCount = */0);
      
  Serial.println("-----------------------------");
  delay(1000);
  
}

void old_delete_finger(){
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

void delete_all(){ //4
  uint8_t ret = 0;
  
  fingerprint.delFingerprint(DELALL);
  fingerprint.ctrlLED(fingerprint.eFastBlink,fingerprint.eLEDRed,5);
  
  Serial.println("-----------------------------");
  delay(1000);
}

void loop(){

// Effacez l'écran LCD
lcd.clear();

// Lire l'état actuel du bouton
boutonState = digitalRead(boutonPin);

// Si l'état du bouton a changé
if (boutonState != lastButtonState) {
  // Enregistrer l'état actuel du bouton
  lastButtonState = boutonState;

  // Si le bouton est enfoncé
  if (boutonState == HIGH) {
    Serial.println("Bouton enfoncé");
    fonction=2; //Si le bouton est enfoncé, on active la fonction match qui est la fonction 2
  } else {
    Serial.println("Bouton relâché");
  }
}
  
server.handleClient();
Serial.print("Fonction: ");
Serial.print(fonction);
Serial.print(", ID: ");
Serial.println(ID);
delay(100);

if(fonction !=9){
  switch(fonction){
    case 1:
    add_finger(ID);
    fingerprint.ctrlLED(fingerprint.eKeepsOn, fingerprint.eLEDWhite,0);
    fonction=9;
    break;
    case 2:
    match_finger();
    fingerprint.ctrlLED(fingerprint.eKeepsOn, fingerprint.eLEDWhite,0);
    fonction=9;
    break;
    case 3:
    delete_finger(ID);
    fingerprint.ctrlLED(fingerprint.eKeepsOn, fingerprint.eLEDWhite,0);
    fonction=9;
    break;
    case 4:
    delete_all();
    fingerprint.ctrlLED(fingerprint.eKeepsOn, fingerprint.eLEDWhite,0);
    fonction=9;
    break;
  }
}

}
