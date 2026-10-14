# Smart-Security-System-Using-Face-Recognition-and-RFID-Based-Dual-Gate-Access
Smart security system using face recognition, YOLOv5, RFID, and ESP32-based dual gate access control.
this system identify user by matching user RFID ID numer and Face.

for this project you will need
1. Pyhton 3.10
2. Yolov5 - https://github.com/ultralytics/yolov5
3. use best.pt as model for yolo
4. Insightface framework installed (arcface)
5. import smartsecurity.sql
   
6. upload ESP32 Code.ino to your ESP32
7. Need two ESP32,
8.  one for entrance and use   : String jsonData = "{\"uid\":\"" + uid + "\",\"gate\":\"masuk\"}";
9.  one for exit and use      : String jsonData = "{\"uid\":\"" + uid + "\",\"gate\":\"keluar\"}";

10. two for each MFRC522 and HCSR-04
11. two MG996R servo
12. two Buzzer
13. Two cameras connected via USB to your computer - webcam supported
14. use your local network (ssid, and passowrd for ESP32)
15. take a look at wiring folder as reference wiring
