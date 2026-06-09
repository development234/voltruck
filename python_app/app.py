import serial # untuk komunikasi dengan LiDAR
import requests # untuk mengirim data ke API Laravel
import json
import time

# --- KONFIGURASI ---
LARAVEL_API_URL = "http://localhost:8000/api/lidar-data"
API_TOKEN = "2|0KErBERJStvvSS6AlI3SW37QJqLuPoDENqt7J6UR3c37c52b" # Ganti dengan token dari user

# --- FUNGSI ---
def read_lidar_data():
    # GANTI DENGAN LOGIKA BACA DATA DARI SENSOR LIDAR ANDA
    # Contoh: ser = serial.Serial('COM3', 115200) # Buka port serial
    #          raw_line = ser.readline()          # Baca satu baris
    # Contoh data dummy yang mensimulasikan format JSON dari LiDAR
    sample_point_cloud = [
        {"x": 0.1, "y": 0.2, "z": 0.5},
        {"x": 0.3, "y": 0.4, "z": 0.6},
        {"x": 0.5, "y": 0.6, "z": 0.8}
    ]
    return json.dumps(sample_point_cloud)

# --- LOOP UTAMA ---
if __name__ == "__main__":
    while True:
        data_to_send = read_lidar_data()
        payload = {
            "sensor_id": "lidar_sensor_01",
            "raw_data": data_to_send,
            "measured_at": time.strftime('%Y-%m-%d %H:%M:%S')
        }
        headers = {
            "Authorization": f"Bearer {API_TOKEN}",
            "Content-Type": "application/json",
            "Accept": "application/json"
        }
        try:
            response = requests.post(LARAVEL_API_URL, json=payload, headers=headers)
            print(f"Status Code: {response.status_code}, Response: {response.json()}")
        except Exception as e:
            print(f"Error sending data: {e}")
        time.sleep(10) # Kirim data setiap 10 detik