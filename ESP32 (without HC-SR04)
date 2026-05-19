#include <WiFi.h>
#include <HTTPClient.h>
#include <SPI.h>
#include <MFRC522.h>
#include <ESP32Servo.h>

#define SS_PIN      5
#define RST_PIN     22
#define SERVO_PIN   13

#define TRIG_PIN    14
#define ECHO_PIN    27

#define BUZZER_PIN 26

const char* ssid = "Brzenweiss";
const char* password = "mgl02806";
const char* serverName = "http://192.168.18.4:5000/rfid";

MFRC522 mfrc522(SS_PIN, RST_PIN);
Servo gateServo;

unsigned long lastReadTime = 0;
const unsigned long COOLDOWN = 2000;

bool gateOpen = false;


// ================= ULTRASONIC FUNCTION =================
long readDistance() {
  digitalWrite(TRIG_PIN, LOW);
  delayMicroseconds(2);

  digitalWrite(TRIG_PIN, HIGH);
  delayMicroseconds(10);
  digitalWrite(TRIG_PIN, LOW);

  long duration = pulseIn(ECHO_PIN, HIGH, 30000); // timeout 30ms
  long distance = duration * 0.034 / 2;

  return distance;
}

// ================= SETUP =================
void setup() {

  Serial.begin(115200);

  SPI.begin();
  mfrc522.PCD_Init();

  gateServo.attach(SERVO_PIN);
  gateServo.write(0); // posisi awal tertutup

  pinMode(TRIG_PIN, OUTPUT);
  pinMode(ECHO_PIN, INPUT);

  pinMode(BUZZER_PIN, OUTPUT);
  digitalWrite(BUZZER_PIN, LOW);

  WiFi.begin(ssid, password);
  Serial.print("Connecting");

  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }

  Serial.println("\nWiFi Connected");
  Serial.println(WiFi.localIP());
}

// ================= BEEP FUNCTIONS =================
void beepGranted() {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(200);              // 1 beep pendek
  digitalWrite(BUZZER_PIN, LOW);
}

void beepDenied() {
  for (int i = 0; i < 3; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(100);            // beep cepat
    digitalWrite(BUZZER_PIN, LOW);
    delay(100);
  }
}

// ================= LOOP =================
void loop() {

  // ================= RFID READ =================
  if (mfrc522.PICC_IsNewCardPresent() && mfrc522.PICC_ReadCardSerial()) {

    unsigned long now = millis();
    if (now - lastReadTime < COOLDOWN) return;
    lastReadTime = now;

    String uid = "";

    for (byte i = 0; i < mfrc522.uid.size; i++) {
      if (mfrc522.uid.uidByte[i] < 0x10) uid += "0";
      uid += String(mfrc522.uid.uidByte[i], HEX);
    }

    uid.toUpperCase();
    Serial.println("UID: " + uid);

    if (WiFi.status() == WL_CONNECTED) {

      HTTPClient http;
      http.begin(serverName);
      http.addHeader("Content-Type", "application/json");

      String jsonData = "{\"uid\":\"" + uid + "\",\"gate\":\"keluar\"}";
      int httpResponseCode = http.POST(jsonData);

      if (httpResponseCode > 0) {

        String response = http.getString();
        Serial.println("Server: " + response);

        if (response.indexOf("granted") != -1) {

          Serial.println("ACCESS GRANTED - OPEN GATE");
          beepGranted();

          gateServo.write(90);
          gateOpen = true;
        }
        else {
          Serial.println("ACCESS DENIED");
          beepDenied();
        }

      } else {
        Serial.println("HTTP Error");
      }

      http.end();
    }

    mfrc522.PICC_HaltA();
    mfrc522.PCD_StopCrypto1();
  }


  // ================= AUTO CLOSE WITH ULTRASONIC =================
  if (gateOpen) {

    long distance = readDistance();

    Serial.print("Distance: ");
    Serial.println(distance);

    // Jika orang terdeteksi (misal < 30cm)
    if (distance > 0 && distance < 30) {

      delay(500); // tunggu stabil

      // Tunggu sampai orang benar-benar lewat
      while (readDistance() < 30) {
        delay(100);
      }

      Serial.println("PERSON PASSED - CLOSE GATE");

      delay(500);
      gateServo.write(0);
      gateOpen = false;
    }
  }
}
