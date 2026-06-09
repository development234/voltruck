import requests
import json
from datetime import datetime

URL = "http://127.0.0.1:8000/api/test"
TOKEN = "3|wr61LBWQXFz52cegQySYMWPMIwMKELFVp0RpcuQU647d1f0a"  # Ganti dengan token valid

payload = {
    "sensor_id": "test",
    "raw_data": "[{\"x\":0,\"y\":0,\"z\":0.5}]",
    "measured_at": datetime.now().strftime("%Y-%m-%d %H:%M:%S")
}

headers = {
    "Authorization": f"Bearer {TOKEN}",
    "Content-Type": "application/json"
}

try:
    resp = requests.post(URL, json=payload, headers=headers, timeout=5)
    print(f"Status: {resp.status_code}")
    print(f"Response: {resp.json()}")
except Exception as e:
    print(f"Error: {e}")