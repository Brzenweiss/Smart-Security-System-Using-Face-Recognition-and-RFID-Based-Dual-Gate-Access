# ============================================================
# SMART SECURITY SYSTEM - DUAL GATE VERSION
# 2 Camera + 2 ESP + Parallel State Machine
# ============================================================

import warnings
warnings.filterwarnings("ignore")

import pathlib
pathlib.PosixPath = pathlib.WindowsPath

import cv2
import torch
import os
import time
import threading
import pickle
import numpy as np
import mediapipe as mp
import mysql.connector
from datetime import datetime
from flask import Flask, request, jsonify
from insightface.app import FaceAnalysis


# ============================================================
# ======================== CONFIG ============================
# ============================================================

YOLO_MODEL_PATH = "best.pt"
CONF_THRESHOLD = 0.4

EMBED_FILE = "embeddings.pkl"
SIM_THRESHOLD = 0.6

BLINK_TIMEOUT = 5
CAPTURE_DELAY = 0.5
PREPARE_DURATION = 0.5

BASE_DIR = os.path.dirname(os.path.abspath(__file__))
FOTO_DB_DIR = os.path.join(BASE_DIR, "foto_pengunjung")
os.makedirs(FOTO_DB_DIR, exist_ok=True)

DB_CONFIG = {
    "host": "localhost",
    "user": "root",
    "password": "",
    "database": "smartsecurity"
}
# load embeddings database
if os.path.exists(EMBED_FILE):
    with open(EMBED_FILE, "rb") as f:
        embeddings_db = pickle.load(f)
else:
    embeddings_db = {}

# ============================================================
# ======================== FLASK SERVER ======================
# ============================================================

app = Flask(__name__)

# ----------------------------
# SETIAP GERBANG PUNYA STATE SENDIRI
# ----------------------------
gates = {
    "masuk": {},
    "keluar": {}
}

# Inisialisasi state default untuk tiap gate
for g in gates:
    gates[g] = {
        "latest_uid": None,
        "verification_result": None,
        "state": 0,
        "current_uid": None,
        "blink_stage": 0,
        "blink_start_time": 0,
        "prepare_start_time": 0,
        "capture_ready_time": 0,
        "process_frame": None,
        "status_text": "SYSTEM READY - TAP RFID",
        "status_color": (0,255,255),
        "status_time": 0
    }


@app.route('/rfid', methods=['POST'])
def receive_rfid():

    data = request.json
    uid = data.get("uid")
    gate = data.get("gate")

    # Pastikan gate valid
    if uid and gate in gates:

        gates[gate]["latest_uid"] = uid.upper()
        gates[gate]["verification_result"] = None

        # Tunggu hasil verifikasi gate tersebut
        start_time = time.time()
        while gates[gate]["verification_result"] is None:
            if time.time() - start_time > 10:
                return jsonify({"status": "timeout"}), 200
            time.sleep(0.1)

        return jsonify({"status": gates[gate]["verification_result"]}), 200

    return jsonify({"status": "error"}), 400


def run_server():
    app.run(host='0.0.0.0', port=5000)

threading.Thread(target=run_server, daemon=True).start()


# ============================================================
# ======================== LOAD MODELS =======================
# ============================================================

model = torch.hub.load('./yolov5', 'custom',
                       source='local',
                       path=YOLO_MODEL_PATH,
                       force_reload=True)
model.conf = CONF_THRESHOLD

arcface = FaceAnalysis(name="buffalo_l")
arcface.prepare(ctx_id=0)

mp_face_mesh = mp.solutions.face_mesh
face_mesh = mp_face_mesh.FaceMesh(
    static_image_mode=False,
    max_num_faces=1,
    refine_landmarks=True
)


# ============================================================
# ======================== DATABASE ==========================
# ============================================================

def insert_log(gate, uid, filename, confidence):
    conn = mysql.connector.connect(**DB_CONFIG)
    cursor = conn.cursor()

    if gate == "masuk":
        sql = "INSERT INTO log_masuk (uid,foto,waktu_masuk,confidence) VALUES (%s,%s,%s,%s)"
    else:
        sql = "INSERT INTO log_keluar (uid,foto,waktu_keluar,confidence) VALUES (%s,%s,%s,%s)"

    cursor.execute(sql, (uid, filename, datetime.now(), confidence))
    conn.commit()
    cursor.close()
    conn.close()


# ============================================================
# ======================== UTIL ==============================
# ============================================================

def cosine_similarity(a, b):
    return np.dot(a, b) / (np.linalg.norm(a) * np.linalg.norm(b))


def eye_aspect_ratio(eye):
    A = np.linalg.norm(eye[1] - eye[5])
    B = np.linalg.norm(eye[2] - eye[4])
    C = np.linalg.norm(eye[0] - eye[3])
    return (A + B) / (2.0 * C)


def get_ear(frame):
    rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
    results = face_mesh.process(rgb)
    if not results.multi_face_landmarks:
        return None

    h, w, _ = frame.shape
    face_landmarks = results.multi_face_landmarks[0]

    LEFT_EYE = [33,160,158,133,153,144]
    RIGHT_EYE = [362,385,387,263,373,380]

    left = np.array([[int(face_landmarks.landmark[i].x*w),
                      int(face_landmarks.landmark[i].y*h)] for i in LEFT_EYE])
    right = np.array([[int(face_landmarks.landmark[i].x*w),
                       int(face_landmarks.landmark[i].y*h)] for i in RIGHT_EYE])

    return (eye_aspect_ratio(left) + eye_aspect_ratio(right)) / 2.0


# ============================================================
# ======================== CAMERA ============================
# ============================================================

cap_masuk = cv2.VideoCapture(0, cv2.CAP_DSHOW)
cap_keluar = cv2.VideoCapture(1, cv2.CAP_DSHOW)


# ============================================================
# ======================== STATE CONSTANT ====================
# ============================================================

STATE_IDLE = 0
STATE_PREPARE = 1
STATE_BLINK = 2
STATE_WAIT_CAPTURE = 3
STATE_PROCESS = 4


# ============================================================
# ======================== MAIN LOOP =========================
# ============================================================

while True:

    ret_masuk, frame_masuk = cap_masuk.read()
    ret_keluar, frame_keluar = cap_keluar.read()

    if not ret_masuk or not ret_keluar:
        break

    # --------------------------------------------------------
    # LOOP UNTUK MASING-MASING GERBANG
    # --------------------------------------------------------
    for gate_name in ["masuk", "keluar"]:

        gate = gates[gate_name]

        # Pilih frame sesuai gate
        frame = frame_masuk if gate_name == "masuk" else frame_keluar
        display_masuk = frame_masuk.copy()
        display_keluar = frame_keluar.copy()

        # ================= CEK RFID =================
        if gate["latest_uid"] and gate["state"] == STATE_IDLE:
            gate["current_uid"] = gate["latest_uid"]
            gate["latest_uid"] = None
            gate["state"] = STATE_PREPARE
            gate["prepare_start_time"] = time.time()
            gate["status_text"] = "SILAHKAN MENGHADAP KAMERA"

        # ================= PREPARE =================
        if gate["state"] == STATE_PREPARE:
            if time.time() - gate["prepare_start_time"] > PREPARE_DURATION:
                gate["state"] = STATE_BLINK
                gate["blink_stage"] = 0
                gate["blink_start_time"] = time.time()
                gate["status_text"] = "SILAHKAN BERKEDIP"

        # ================= BLINK =================
        if gate["state"] == STATE_BLINK:

            ear = get_ear(frame)

            if ear is not None:
                if ear < 0.21 and gate["blink_stage"] == 0:
                    gate["blink_stage"] = 1
                elif ear > 0.23 and gate["blink_stage"] == 1:
                    gate["capture_ready_time"] = time.time() + CAPTURE_DELAY
                    gate["state"] = STATE_WAIT_CAPTURE

            if time.time() - gate["blink_start_time"] > BLINK_TIMEOUT:
                gate["verification_result"] = "denied"
                gate["state"] = STATE_IDLE
                gate["status_text"] = "TIMEOUT"

        # ================= WAIT CAPTURE =================
        if gate["state"] == STATE_WAIT_CAPTURE:
            if time.time() >= gate["capture_ready_time"]:
                gate["process_frame"] = frame.copy()
                gate["state"] = STATE_PROCESS

        # ================= PROCESS =================
        if gate["state"] == STATE_PROCESS:

            results = model(gate["process_frame"])
            detections = results.pandas().xyxy[0]

            if len(detections) > 0:

                #faces = arcface.get(gate["process_frame"])
                best = detections.sort_values(by='confidence', ascending=False).iloc[0]
                x1, y1, x2, y2 = int(best['xmin']), int(best['ymin']), int(best['xmax']), int(best['ymax'])

                face_crop = gate["process_frame"][y1:y2, x1:x2]
                
                faces = arcface.get(gate["process_frame"])

                if len(faces) > 0:

                    embedding = faces[0].embedding

                    # Register database embeddings
                    if gate["current_uid"] not in embeddings_db:

                        embeddings_db[gate["current_uid"]] = embedding
                        with open(EMBED_FILE, "wb") as f:
                            pickle.dump(embeddings_db, f)
                        confidence = 1.0

                        gate["verification_result"] = "granted"
                        gate["status_text"] = "ACCESS GRANTED (NEW USER)"
                        gate["status_color"] = (0,255,0)
                        gate["status_time"] = time.time()

                    # Verifikasi dengan database
                    else: 
                        sim = cosine_similarity(embedding, embeddings_db[gate["current_uid"]])
                        
                        confidence = float(sim)
                        if sim > SIM_THRESHOLD:
                            gate["verification_result"] = "granted"
                            gate["status_text"] = f"ACCESS GRANTED ({sim:.2f})"
                            gate["status_color"] = (0,255,0)
                            gate["status_time"] = time.time()
                        else:
                            gate["verification_result"] = "denied"
                            gate["status_text"] = f"ACCESS DENIED ({sim:.2f})" 
                            gate["status_color"] = (0,0,255)
                            gate["status_time"] = time.time()
                        
                        print("UID RFID:", gate["current_uid"])
                        print("UID di embeddings_db:", embeddings_db.keys())


                    filename = f"{gate['current_uid']}_{int(time.time())}.jpg"
                    full_path = os.path.join(FOTO_DB_DIR, filename)


                    cv2.imwrite(full_path, face_crop)
                    if gate["verification_result"] == "granted":
                        insert_log(gate_name, gate["current_uid"], filename, confidence)


                else:
                    gate["verification_result"] = "denied"
                    gate["status_text"] = "FACE FAIL"
                    gate["status_color"] = (0,0,255)
                    gate["status_time"] = time.time()

            else:
                gate["verification_result"] = "denied"
                gate["status_text"] = "NO FACE"
                gate["status_color"] = (0,0,255)
                gate["status_time"] = time.time()

            # Reset gate setelah selesai
            gate["state"] = STATE_IDLE
            gate["current_uid"] = None
            gate["process_frame"] = None
            
            

        # Tampilkan status per kamera

        cv2.rectangle(display_masuk, (0,0), (350,90), (0,0,0), -1)
        cv2.rectangle(display_keluar, (0,0), (350,90), (0,0,0), -1)

        cv2.putText(display_masuk, "GERBANG MASUK", (10,30), cv2.FONT_HERSHEY_SIMPLEX, 1, (0,255,255), 2)
        cv2.putText(display_keluar, "GERBANG KELUAR", (10,30), cv2.FONT_HERSHEY_SIMPLEX, 1, (0,255,255), 2)

        cv2.putText(display_masuk, gates["masuk"]["status_text"], (10,70), cv2.FONT_HERSHEY_SIMPLEX, 0.7, gates["masuk"]["status_color"], 2)
        cv2.putText(display_keluar, gates["keluar"]["status_text"], (10,70), cv2.FONT_HERSHEY_SIMPLEX, 0.7, gates["keluar"]["status_color"], 2)
        
        if gate["state"] == STATE_IDLE and gate["verification_result"] is not None:
            if time.time() - gate["status_time"] > 2:
                gate["status_text"] = "SYSTEM READY - TAP RFID"
                gate["status_color"] = (0,255,255)
                gate["verification_result"] = None

        cv2.imshow("CAMERA MASUK", display_masuk)
        cv2.imshow("CAMERA KELUAR", display_keluar)

        cv2.moveWindow("CAMERA MASUK", 100, 100)
        cv2.moveWindow("CAMERA KELUAR", 800, 100)

    if cv2.waitKey(1) & 0xFF == ord('q'):
        break


cap_masuk.release()
cap_keluar.release()
cv2.destroyAllWindows()