# Smart-Security-System-Using-Face-Recognition-and-RFID-Based-Dual-Gate-Access
Smart security system using face recognition, YOLOv5, RFID, and ESP32-based dual gate access control.

for this project you will need
1. Yolov5 - https://github.com/ultralytics/yolov5
2. use best.pt as model for yolo
3. run the app from main.py
4. import smartsecurity.sql
5. upload ESP32 Code to your ESP
6. Need two ESP,
   one for entrance and use gate: String jsonData = "{\"uid\":\"" + uid + "\",\"gate\":\"masuk\"}";
   one for exit and use gate: String jsonData = "{\"uid\":\"" + uid + "\",\"gate\":\"keluar\"}";
7. Two cameras connected via USB to your computer
