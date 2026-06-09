import requests
import json
import time
import math
import random
from datetime import datetime

URL = "http://127.0.0.1:8000/api/lidar-data"
TOKEN = "3|wr61LBWQXFz52cegQySYMWPMIwMKELFVp0RpcuQU647d1f0a"  # Ganti jika perlu

def test_connection():
    try:
        resp = requests.get("http://127.0.0.1:8000", timeout=10)
        print(f"✅ Server Laravel merespon (status {resp.status_code})")
        return True
    except requests.exceptions.Timeout:
        print("❌ Timeout: Server Laravel tidak merespon dalam 10 detik.")
        return False
    except requests.exceptions.ConnectionError:
        print("❌ Tidak dapat terhubung. Pastikan server berjalan di 127.0.0.1:8000")
        return False
    except Exception as e:
        print(f"❌ Error koneksi: {e}")
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
        resp = requests.post(URL, json=payload, headers=headers, timeout=15)
        if resp.status_code in (200, 201, 202):
            print(f"✅ [{resp.status_code}] Data terkirim. ID: {resp.json().get('lidar_raw_data_id', '?')}")
            return True
        else:
            print(f"❌ [{resp.status_code}] {resp.text[:200]}")
            return False
    except requests.exceptions.Timeout:
        print("❌ Timeout: Server terlalu lama merespon. Cek log Laravel.")
        return False
    except Exception as e:
        print(f"❌ Error: {e}")
        return False

if __name__ == "__main__":
    print("=" * 60)
    print("SIMULASI LIDAR - PENGUKURAN VOLUME TRUK")
    print("=" * 60)
    print(f"Endpoint: {URL}")
    print(f"Token: {TOKEN[:10]}...{TOKEN[-10:]}")
    print("-" * 60)
    
    if not test_connection():
        exit(1)
    
    print("\n🔍 Mengirim data uji coba (1 titik)...")
    test_points = [{"x": 0, "y": 0, "z": 0.5}]
    if not send_measurement(test_points):
        print("\n⚠️ Gagal mengirim data uji. Periksa:")
        print("  1. Server Laravel (php artisan serve) berjalan?")
        print("  2. Token API (generate ulang di Tinker)")
        print("  3. Database dan migration sudah dijalankan?")
        exit(1)
    
    print("\n✅ Siap memulai simulasi. Kirim data setiap 10 detik.\n")
    counter = 1
    while True:
        # Generate point cloud berbentuk kerucut (400 titik)
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