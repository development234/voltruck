import requests
import json
import time
import math
import random
from datetime import datetime

# ========== KONFIGURASI ==========
API_URL = "http://127.0.0.1:8000/api/lidar-data"
TOKEN = "3|wr61LBWQXFz52cegQySYMWPMIwMKELFVp0RpcuQU647d1f0a"  # Ganti dengan token valid

# ========== DATA TRUK (10 buah) ==========
trucks = [
    {"plate": "AB 2001 CD", "driver": "Budi Santoso", "length": 4.2, "width": 2.1, "height": 1.5, "volume_target": 4.0},
    {"plate": "B 1234 XYZ", "driver": "Ahmad Fahrudin", "length": 5.0, "width": 2.2, "height": 1.6, "volume_target": 6.5},
    {"plate": "D 5678 EFG", "driver": "Siti Nurjanah", "length": 3.8, "width": 1.9, "height": 1.4, "volume_target": 3.2},
    {"plate": "E 9101 HIJ", "driver": "Joko Widodo", "length": 4.5, "width": 2.0, "height": 1.5, "volume_target": 5.0},
    {"plate": "F 1122 KLM", "driver": "Rina Melati", "length": 5.2, "width": 2.3, "height": 1.7, "volume_target": 7.0},
    {"plate": "G 3344 NOP", "driver": "Eko Prasetyo", "length": 4.0, "width": 2.0, "height": 1.4, "volume_target": 3.8},
    {"plate": "H 5566 QRS", "driver": "Dewi Lestari", "length": 4.8, "width": 2.1, "height": 1.6, "volume_target": 5.5},
    {"plate": "I 7788 TUV", "driver": "Agus Salim", "length": 3.5, "width": 1.8, "height": 1.3, "volume_target": 2.5},
    {"plate": "J 9900 WXYZ", "driver": "Rizki Febrian", "length": 5.5, "width": 2.4, "height": 1.8, "volume_target": 8.0},
    {"plate": "K 2468 ABC", "driver": "Maya Sari", "length": 4.3, "width": 2.2, "height": 1.6, "volume_target": 4.8}
]

# ========== FUNGSI PEMBANGKIT POINT CLOUD ==========
def generate_load_point_cloud(length, width, height, volume_target, points_count=800):
    """
    Membangkitkan titik-titik muatan dalam bak truk.
    - Muatan diasumsikan berbentuk kerucut terpancung (tumpukan pasir) di tengah bak.
    - Volume target (m³) digunakan untuk skala tinggi muatan.
    - Fungsi mengembalikan list of dict {"x": float, "y": float, "z": float}
    """
    # Luas alas bak
    base_area = length * width
    # Tinggi muatan ideal jika penuh rata = volume_target / base_area
    avg_height = volume_target / base_area
    # Maks tinggi di puncak (1.5 kali tinggi rata-rata, tidak melebihi tinggi bak)
    max_height = min(height, avg_height * 1.8)
    
    points = []
    # Titik dasar (alas) - random di seluruh permukaan bak
    for _ in range(int(points_count * 0.2)):  # 20% titik di dasar
        x = random.uniform(-length/2, length/2)
        y = random.uniform(-width/2, width/2)
        z = random.uniform(0, 0.05)  # dasar dekat 0
        points.append({"x": x, "y": y, "z": z})
    
    # Titik muatan (tumpukan kerucut)
    for _ in range(points_count - int(points_count * 0.2)):
        # Distribusi radial (lebih padat di tengah)
        r = math.sqrt(random.uniform(0, 1)) * (min(length, width) / 2.2)
        angle = random.uniform(0, 2 * math.pi)
        x = r * math.cos(angle)
        y = r * math.sin(angle)
        # Pastikan masih dalam batas bak (kliping)
        x = max(-length/2, min(length/2, x))
        y = max(-width/2, min(width/2, y))
        # Tinggi muatan: linear menurun dari pusat ke tepi
        radius_max = min(length/2, width/2)
        if radius_max <= 0:
            radius_max = 1
        ratio = r / radius_max
        z = max_height * (1 - ratio) + random.gauss(0, 0.02)
        z = max(0, min(height, z))
        points.append({"x": x, "y": y, "z": z})
    
    # Acak urutan titik (optional)
    random.shuffle(points)
    return points

# ========== FUNGSI KIRIM DATA ==========
def send_measurement(truck_info, points):
    """Kirim data truk dan point cloud ke API Laravel"""
    # Bentuk payload sesuai API: raw_data adalah string JSON yang berisi points + metadata
    raw_data_payload = {
        "points": points,
        "truck": truck_info["plate"],
        "driver": truck_info["driver"],
        "cubic_meters": truck_info["volume_target"],
        "length": truck_info["length"],
        "width": truck_info["width"],
        "height": truck_info["height"]
    }
    payload = {
        "sensor_id": "simulator_01",
        "raw_data": json.dumps(raw_data_payload),
        "measured_at": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    }
    headers = {
        "Authorization": f"Bearer {TOKEN}",
        "Content-Type": "application/json"
    }
    try:
        resp = requests.post(API_URL, json=payload, headers=headers, timeout=15)
        if resp.status_code in (200, 201, 202):
            print(f"✅ [{resp.status_code}] {truck_info['plate']} - {truck_info['driver']} -> volume target {truck_info['volume_target']} m³")
            return True
        else:
            print(f"❌ [{resp.status_code}] {truck_info['plate']} gagal: {resp.text[:100]}")
            return False
    except Exception as e:
        print(f"❌ Error untuk {truck_info['plate']}: {e}")
        return False

# ========== MAIN ==========
if __name__ == "__main__":
    print("=" * 70)
    print("SIMULASI LIDAR - 10 TRUK DENGAN POINT CLOUD REALISTIS")
    print("=" * 70)
    print(f"Endpoint: {API_URL}")
    print(f"Token: {TOKEN[:10]}...{TOKEN[-10:]}")
    print("-" * 70)
    
    # Uji koneksi awal
    try:
        requests.get("http://127.0.0.1:8000", timeout=5)
        print("✅ Server Laravel terhubung.\n")
    except:
        print("⚠️ Server Laravel tidak merespon. Pastikan php artisan serve berjalan.\n")
        exit(1)
    
    # Kirim data untuk setiap truk
    for idx, truck in enumerate(trucks, start=1):
        print(f"\n🚛 Truk {idx}: {truck['plate']} (Sopir: {truck['driver']})")
        print(f"   Ukuran bak: {truck['length']}m x {truck['width']}m x {truck['height']}m")
        print(f"   Target volume: {truck['volume_target']} m³")
        print("   Membangkitkan point cloud...")
        points = generate_load_point_cloud(
            length=truck['length'],
            width=truck['width'],
            height=truck['height'],
            volume_target=truck['volume_target'],
            points_count=800
        )
        print(f"   Jumlah titik: {len(points)}")
        print("   Mengirim ke API...")
        send_measurement(truck, points)
        time.sleep(2)  # jeda antar truk agar tidak overload
    
    print("\n" + "=" * 70)
    print("✅ Simulasi selesai. Data 10 truk telah dikirim.")
    print("Buka http://127.0.0.1:8000/measurement untuk melihat hasil.")