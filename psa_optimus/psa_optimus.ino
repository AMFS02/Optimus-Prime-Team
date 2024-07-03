/*-----------INITIALIZATION-----------*/
#include <WiFiNINA.h>
#include <Servo.h>

//-------- Access Point Setup
char ssid[] = "network";    // Access point SSID
char pass[] = "password";   // Access point password
WiFiServer server(80);

//-------- Website Connection Variables to Read Direction Variables 
int    HTTP_PORT   = 80;
char   HOST_NAME[] = "192.168.4.250";

String PATH_NAME   = "/motor_ler.php";
String HTTP_METHOD = "GET";

//-------- Initialize Variables to Read From Txt File
String valor = "";
String text;
String strs[6]; 
int a = 0;
int ledPin = 13;     // LED Pin
int relay = A2;      // Eletroiman Relay Pin  

//------- Stepper Motor
// Stepper Motor Control Ports
#define ENC_RIGHTMOTOR_PIN 2  // Enconder Right Motor
#define PWM_RIGHTMOTOR 6      // PWM Right Motor
#define DIR_RIGHTMOTOR 8      // Direction Right Motor
#define ENC_LEFTMOTOR_PIN 4   // Encoder Left Motor      
#define PWM_LEFTMOTOR 3       // PWM Left Motor
#define DIR_LEFTMOTOR 9       // Direction Left Motor

// Initialize Variables for Stepper Motor Control
bool flag = HIGH;    // Clock-wise/Counter clock-wise              
int count = 0;       // Encoder                  
int value_vel = 100; // Speed
int vel = 100; int centro_servo = 85;
String readString;

// Initialization of Functions for Stepper Motor Control
void MotorStop();
void RIGHTMotorForward(int vel);
void RIGHTMotorBackwards(int vel);
void LEFTMotorForward(int vel);
void LEFTMotorBackwards(int vel);

int modo = 0;
int modeop = 0;

/// Ultrasom 
const int trigPin = 7; const int echoPin = 12;
float durat, distance;

/// Servo do Ultrasom 
int temprot = 150; int pos = 90;  // posição inicial do servo do ultrasom
int dseg = 20;       // distancia (cm) entre o robo e o objeto a frente
int ir_sens = 450;             // sensibilidade do sensor de Infravermelho

/// Infravermelhos
int ir_esq = A1;  int ir_dir = A3; int iresqval; int irdirval;

// Stepper Motor Rotation Counter Function
void isr(){ if (flag){ count++; } else { count--; } }

//------- Servo Motor
// Initialize Variables for Servo Motor Control
Servo myservo;
const int serPin1 =  5; // Pino digital para o primeiro servo
const int serPin2 = 10; // Pino digital para o segundo servo
const int serPin3 = 11; // Pino digital para o terceiro servo

Servo ser0;   // Ultrasonic Sensor Servo
Servo ser1;   // Swing
Servo ser2;   // Boom 
Servo ser3;   // 

int iniPos1 = 0;
int iniPos2 = 25;
int iniPos3 = 0;

/*-----------SET UP FUNCTION-----------*/
void setup() {
  //----- Serial Communication
    Serial.begin(9600);

    // Pin Port Configuration
    pinMode(ledPin, OUTPUT);
    pinMode(relay,OUTPUT);
    pinMode(ir_esq, INPUT); pinMode(ir_dir, INPUT);

    myservo.attach(13);
    for(pos=20; pos<=160; pos += 5) { myservo.write(pos); delay(temprot); }
    for(pos=160; pos>=centro_servo; pos -= 5) { myservo.write(pos); delay(temprot); }

    // Initialize WiFi Access Point
    WiFi.beginAP(ssid, pass);
    Serial.println("Access Point Started");
    Serial.print("IP Address: ");
    Serial.println(WiFi.localIP());
    server.begin();

    //------ Stepper Motor Set Up
    // Adjusts PWM frequency to 25 kH, on Arduino WiFi
    TCB0_CTRLA = (TCB_CLKSEL_CLKDIV1_gc) | (TCB_ENABLE_bm);  
    TCB1_CTRLA = (TCB_CLKSEL_CLKDIV1_gc) | (TCB_ENABLE_bm);
    TCB2_CTRLA = (TCB_CLKSEL_CLKDIV1_gc) | (TCB_ENABLE_bm);

    ///------ Stepper Motor Set Up
    // Adjusts PWM frequency to 25 kH, on Arduino WiFi
    TCB0_CTRLA = (TCB_CLKSEL_CLKDIV1_gc) | (TCB_ENABLE_bm);  
    TCB1_CTRLA = (TCB_CLKSEL_CLKDIV1_gc) | (TCB_ENABLE_bm);
    TCB2_CTRLA = (TCB_CLKSEL_CLKDIV1_gc) | (TCB_ENABLE_bm);

    /// Stepper Pin Configurations
    pinMode(PWM_RIGHTMOTOR, OUTPUT); pinMode(PWM_LEFTMOTOR, OUTPUT);
    pinMode(DIR_RIGHTMOTOR, OUTPUT); pinMode(DIR_LEFTMOTOR, OUTPUT);

    pinMode(ENC_RIGHTMOTOR_PIN, INPUT_PULLUP); pinMode(ENC_LEFTMOTOR_PIN, INPUT_PULLUP);

    attachInterrupt(digitalPinToInterrupt(ENC_RIGHTMOTOR_PIN), isr, RISING);
    attachInterrupt(digitalPinToInterrupt(ENC_LEFTMOTOR_PIN), isr, RISING);

    // Stop the Stepper Motor
    analogWrite(PWM_RIGHTMOTOR, 255); 
    analogWrite(PWM_LEFTMOTOR, 255);

    //----- Servo Motors
    pinMode(serPin1, OUTPUT); // Define os pinos dos servos como saídas
    pinMode(serPin2, OUTPUT);
    pinMode(serPin3, OUTPUT);

    myservo.attach(13); // Ultrasound Servo
    ser1.attach(serPin1);
    ser2.attach(serPin2);
    ser3.attach(serPin3);

    GripDefault();

    //----- Ultrassound
    pinMode(trigPin, OUTPUT);
    pinMode(echoPin, INPUT);
}

/*-----------FUNCTIONS-----------*/
///----------- MOTORES -----------///
  /// Stepper Motor Stop Function
  void MotorStop() { 
    digitalWrite(DIR_RIGHTMOTOR, LOW);   analogWrite(PWM_RIGHTMOTOR, 255); 
    digitalWrite(DIR_LEFTMOTOR, LOW);    analogWrite(PWM_LEFTMOTOR, 255);
  }

  /// Stepper Motor Front Function
  void MotorFront(int vel) { 
    digitalWrite(DIR_RIGHTMOTOR, HIGH);   analogWrite(PWM_RIGHTMOTOR, vel); 
    digitalWrite(DIR_LEFTMOTOR, LOW);     analogWrite(PWM_LEFTMOTOR, vel);
  }

  /// Stepper Motor Front Function
  void MotorBack(int vel) { 
    digitalWrite(DIR_RIGHTMOTOR, LOW);    analogWrite(PWM_RIGHTMOTOR, vel); 
    digitalWrite(DIR_LEFTMOTOR, HIGH);    analogWrite(PWM_LEFTMOTOR, vel);
  }

  /// Left Stepper Motor Control
  void LEFTMotorBackwards(int vel) {    // Backwards Function
    digitalWrite(DIR_LEFTMOTOR, LOW);   // Set direction for backwards
    analogWrite(PWM_LEFTMOTOR, vel);    // Set PWM for speed control
  }
  void LEFTMotorForward(int vel) {      // Forward Function
    digitalWrite(DIR_LEFTMOTOR, HIGH);  // Set direction for forward
    analogWrite(PWM_LEFTMOTOR, vel);    // Set PWM for speed control
  }

  /// Right Stepper Motor Control
  void RIGHTMotorBackwards(int vel) {   // Backwards Function
    digitalWrite(DIR_RIGHTMOTOR, HIGH); // Set direction for backwards
    analogWrite(PWM_RIGHTMOTOR, vel);   // Set PWM for speed control
  }
  void RIGHTMotorForward(int vel) {     // Forward Function
    digitalWrite(DIR_RIGHTMOTOR, LOW);  // Set direction for forward
    analogWrite(PWM_RIGHTMOTOR, vel);   // Set PWM for speed control
  }

// ----- Servo Motors
void GripDefault(void) {
  ser1.write(iniPos1); delay(1000);
  ser2.write(iniPos2); delay(1000);
  ser3.write(iniPos3); delay(1000);
}

// Look Around For Obstacles
void scan(){ // Scan com Ultrassom
    delayMicroseconds(2);  digitalWrite(trigPin, HIGH);
    delayMicroseconds(10); digitalWrite(trigPin, LOW);
    durat = pulseIn(echoPin, HIGH); distance = (durat*.0343)/2;
  }

  void irline(){ // codificação de estados para a escala 0 e 1
    iresqval = analogRead(ir_esq); irdirval = analogRead(ir_dir);
    
    if(iresqval <= ir_sens) { iresqval = 0; } else { iresqval = 1; } // linha preta
    if(irdirval <= ir_sens) { irdirval = 0; } else { irdirval = 1; } // linha preta
  }

  void stay(){ MotorStop(); delay(1000);}

  // Hold on movement function - to keep a certain movement for a certain amount of time
  void dofor(int time){delay(time);  MotorStop(); delay(20);}


/*-----------MAIN-----------*/
void loop() {
  digitalWrite(ledPin, 0);
  WiFiClient client = server.available();

  if(client.connect(HOST_NAME, HTTP_PORT)) {         
      // Make a HTTP request
      client.println(HTTP_METHOD + " " + PATH_NAME + valor + " HTTP/1.1");
      client.println("Host: " + String(HOST_NAME));

      text = "";

      if(client.connected()) {
        digitalWrite(ledPin, 1);          // When client connects to the server, the LED turns on
        text = client.readString();      // Starts reading the txt file

        //------------- AUTOMATIC MODE 
        if(text.indexOf("auto") > -1){ 
          
              int val_ultsom = 0;

          /// codificação de estados para a escala 0 e 1
            /// val_ultsom = 0 -> não detetou nenhum objeto
            /// val_ultsom = 1 -> detetou um objeto
          if(val_ultsom <= dseg){ val_ultsom = 1; } else {val_ultsom = 0;}

          iresqval = analogRead(ir_esq); irdirval = analogRead(ir_dir);

          /// codificação de estados para a escala 0 e 1
          if(iresqval <= ir_sens) { iresqval = 0; } else { iresqval = 1; } // linha preta
          if(irdirval <= ir_sens) { irdirval = 0; } else { irdirval = 1; } // linha preta
        
          do{
          //----------- modeop 0 - modeop Automático default
            delay(5); scan(); irline();

            /// modeop segue linha 
            if(iresqval == 0 && irdirval == 0){stay();}
            if(iresqval == 0 && irdirval == 1){LEFTMotorBackwards(120); RIGHTMotorBackwards(90); delay(5);   modeop==0;}
            if(iresqval == 1 && irdirval == 0){LEFTMotorBackwards(90); RIGHTMotorBackwards(120); delay(5);   modeop==0;}
            if(iresqval == 1 && irdirval == 1){LEFTMotorBackwards(vel); RIGHTMotorBackwards(vel); delay(5); modeop==0;}
            if(distance <= 20){stay(); modeop==1;}

          } while(modeop==0); // modeop 0 - modeop Automático default

          do{
          //----------- modeop 1 - Contorno de Objetos
          scan(); irline();
          
          if(iresqval == 1 && irdirval == 0){ modeop=11;} // é um canto 
            else{ modeop = 12; } // é uma caixa
            
          do{
          //----------- modeop 1.1 - Contorno de Canto
            stay();
            LEFTMotorForward(0);     RIGHTMotorForward(0);     dofor(200);  stay();
            LEFTMotorBackwards(0);   RIGHTMotorBackwards(255); dofor(750);  stay();
            LEFTMotorBackwards(vel); RIGHTMotorBackwards(vel); dofor(1200);
            MotorStop(); delay(5000);

            modeop = 0; // modeop 0 - modeop Automático default

          } while(modeop == 11);

          do{
          //----------- modeop 1.2 - Contorno de Caixa
              digitalWrite(A5,HIGH);
              myservo.write(95); scan();

              int distancia = distance;
              myservo.detach(); myservo.attach(13);
              for(pos=centro_servo; pos>=0; pos -= 5){ myservo.write(pos); delay(temprot); }
              
              // 1 roda direita
              LEFTMotorBackwards(90); RIGHTMotorForward(160); delay(750);
              LEFTMotorBackwards(100); RIGHTMotorBackwards(100); delay(200); stay();
              LEFTMotorBackwards(100); RIGHTMotorBackwards(100); delay(300); stay();

              // 2 vai em frente
              do {
                  //myservo.detach(); myservo.attach(13); myservo.write(20);
                  LEFTMotorBackwards(100); RIGHTMotorBackwards(100); delay(800);      
                  scan(); delay(100);

                  // correção da rota
                  if(distance<40){ stay(); LEFTMotorBackwards(100); RIGHTMotorForward(100); delay(100); }
              } while(distance<40);

              // 3 roda para a esquerda
              stay(); LEFTMotorForward(100); RIGHTMotorBackwards(100); delay(700); stay();

              // 4 vai em frente
              do {
                  // myservo.detach(); myservo.attach(13); myservo.write(20);
                  LEFTMotorBackwards(110); RIGHTMotorBackwards(90); delay(400);
                  
                  scan(); delay(100);

                  // correção da rota
                  if(distance<30){ stay(); LEFTMotorBackwards(120); RIGHTMotorForward(110); delay(100);}
              } while(distance<40);

              stay(); LEFTMotorBackwards(120); RIGHTMotorBackwards(110); delay(500); stay();

              // 5 roda para a esquerda
              LEFTMotorForward(120); RIGHTMotorBackwards(120); delay(750); stay();

              // 6 vai em frente
              myservo.detach(); myservo.attach(13);
              for(pos=20; pos<=centro_servo; pos += 5){ myservo.write(pos); delay(temprot); }

              do {
                  //myservo.detach(); myservo.attach(13); myservo.write(centro_servo);
                  LEFTMotorBackwards(100); RIGHTMotorBackwards(100); delay(200);
                  scan(); delay(100);
              } while(distance>25);

              // 7 roda para a direita
              stay(); LEFTMotorBackwards(100); RIGHTMotorForward(100); delay(700); stay();
            
            modeop = 0; // modeop 0 - modeop Automático default

          } while(modeop == 12); 

        } while(modeop == 1); // modeop 1 - modeop Contorno Obstáculos
        
              
        //------------- MANUAL MODE - CAR CONTROL
        } else {
          MotorStop(); // Safety Measure - The Stepper Motors Always Start Turned off
          int ctn1 = 0;
          int convvel = 0;

          // Reading string and divinding the text everytime a ";" is detected
        while(text.length()>0){
          int index = text.indexOf(';');
          if(index == -1){
            strs[1] = "sr";
            strs[ctn1++] = text;
            break;
          } else {
            strs[ctn1++] = text.substring(0, index);
            text = text.substring(index + 1);            
          }
        }

        if(strs[1] == "sr"){// If the transcription was done correctly
          if(strs[2] == "velo"){
            convvel = strs[3].toInt() ;               // Car Speed
            value_vel = 255 - 255*convvel/100;
          }
        } 
        else if(text.indexOf("btdireita") > -1){          // Right
        // ----- Stepper Motors
          RIGHTMotorForward(value_vel);
          LEFTMotorBackwards(value_vel);
        } 
        else if(text.indexOf("btesquerda") > -1){  // Left
          RIGHTMotorBackwards(value_vel);
          LEFTMotorForward(value_vel);
        } 
        else if(text.indexOf("btcima") > -1){      // Forward
          RIGHTMotorForward(value_vel);
          LEFTMotorForward(value_vel);
        } 
        else if(text.indexOf("btbaixo") > -1){     // Backwards
          RIGHTMotorBackwards(value_vel);
          LEFTMotorBackwards(value_vel);
        } 
        else if(text.indexOf("btparar") > -1){     // Stop Car
          MotorStop();
        }

      // Arm control
      // --- Servo Motors
          int ctn = 0;
          int index = 0;
          
          // Reading string and divinding the text everytime a ";" is detected
          while(text.length()>0){
            index = text.indexOf(';');
            if(index == -1){
              strs[1] = "sr";
              strs[ctn++] = text;
              break;
            } 
            else {
              strs[ctn++] = text.substring(0, index);
              text = text.substring(index + 1);            
            }
          }
          
          // Atribute variables for servo control
          if(strs[1] == "sr"){                       // If the transcription was done correctly
            int base = strs[2].toInt(); ser1.write(base);
            int art1 = strs[3].toInt(); ser2.write(art1);
            int art2 = strs[4].toInt(); ser3.write(art2);
            int iman = strs[5].toInt(); if(iman == 1){digitalWrite(relay,HIGH);} else{digitalWrite(relay,LOW);}
          }
    
        }
      
      client.stop();
    } 
    else { Serial.println("Err"); } 
    delay(25);
  }
}