import requests
import json
import time
import math
import random
from datetime import datetime

URL = "http://127.0.0.1:8000/api/lidar-data"  # Ganti ke 127.0.0.1 lebih stabil
TOKEN = "3|wr61LBWQXFz52cegQySYMWPMIwMKELFVp0RpcuQU647d1f0a"  # Ganti dengan token baru yang valid

def test_connection():
    try:
        resp = requests.get("http://127.0.0.1:8000", timeout=3)
        print(f"✅ Server Laravel merespon (status {resp.status_code})")
        return True
    except Exception as e:
        print(f"❌ Tidak bisa terhubung ke server: {e}")
        return False

def send_measurement(points):
    payload = {
        "sensor_id": "simulator_01",
        "raw_data": json.dumps(points),
        "measured_at": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }
    headers = {
        "Authorization": f"Bearer {TOKEN}",
        "Content-Type": "application/json"
    }
    try:
        resp = requests.post(URL, json=payload, headers=headers, timeout=5)
        if resp.status_code in (200, 201, 202):
            print(f"✅ [{resp.status_code}] Data terkirim. ID: {resp.json().get('lidar_raw_data_id', '?')}")
            return True
        else:
            print(f"❌ [{resp.status_code}] {resp.text}")
            return False
    except requests.exceptions.ConnectionError:
        print("❌ Connection error: Pastikan Laravel berjalan di 127.0.0.1:8000")
        return False
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    print("=" * 50)
    print("SIMULASI LIDAR - PENGECEKAN KONEKSI")
    print("=" * 50)
    if not test_connection():
        exit(1)
    
    print("\nMengirim data uji coba...")
    test_points = [{"x": 0, "y": 0, "z": 0.5}]
    if not send_measurement(test_points):
        print("\n⚠️ Gagal mengirim data uji. Periksa:")
        print("  1. Token API (jalankan php artisan tinker untuk generate token baru)")
        print("  2. Pastikan user yang punya token masih aktif")
        print("  3. Cek tabel personal_access_tokens di database")
        exit(1)
    
    print("\n✅ Siap memulai simulasi. Kirim data setiap 10 detik.\n")
    counter = 1
    while True:
        # Generate point cloud (cone)
        points = []
        radius = 1.4
        height = 0.9
        for _ in range(400):
            r = random.uniform(0, radius) ** 1.2
            theta = random.uniform(0, 2*math.pi)
            x = r * math.cos(theta)
            y = r * math.sin(theta)
            z = height * (1 - r/radius) + random.gauss(0, 0.02)
            z = max(0, z)
            points.append({"x": x, "y": y, "z": z})
        
        print(f"[{counter}] Mengirim {len(points)} titik (muatan kerucut)")
        send_measurement(points)
        counter += 1
        time.sleep(10)