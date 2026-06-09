@extends('layouts.app')

@section('title', 'Scanner Truk')

@section('content')
<div class="container-fluid mt-0">
    <div class="row g-4">
        <!-- Kolom 1: Form -->
        <div class="col-md-4">
            <div class="card card-voltruck h-100">
                <div class="card-header bg-transparent border-bottom border-warning">
                    <h5 class="mb-0 text-warning"><i class="bi bi-qr-code-scan"></i> Form Scan Truk</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('scanner.store') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label text-warning">
                                Pilih Truk <span class="text-danger">*</span>        </select>
                                <button type="button" class="btn btn-sm btn-outline-warning text-end" data-bs-toggle="modal" data-bs-target="#scanPlateModal">
                                    <i class="bi bi-camera-fill"></i> Scan Plat
                                </button>
                            </label>
                            <select name="truck_id" id="truckSelect" class="form-select bg-dark text-white border-warning @error('truck_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Truk --</option>
                                @foreach($trucks as $truck)
                                    <option value="{{ $truck->id }}" data-length="{{ $truck->length_m }}" data-width="{{ $truck->width_m }}" data-height="{{ $truck->height_m }}"
                                        {{ old('truck_id') == $truck->id ? 'selected' : '' }}>
                                        {{ $truck->plate_number }} - {{ $truck->name }} ({{ $truck->length_m }}x{{ $truck->width_m }}x{{ $truck->height_m }} m)
                                    </option>
                                @endforeach
                            </select>
                            @error('truck_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-warning">Pilih Driver (opsional)</label>
                            <select name="driver_id" class="form-select bg-dark text-white border-warning @error('driver_id') is-invalid @enderror">
                                <option value="">-- Pilih Driver --</option>
                                @foreach($drivers as $driver)
                                    <option value="{{ $driver->id }}" {{ old('driver_id') == $driver->id ? 'selected' : '' }}>
                                        {{ $driver->name }} ({{ $driver->license_number ?? 'No SIM' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('driver_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-warning">Volume Hasil Scan (m³) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="result_volume_m3" id="volumeManualInput" class="form-control bg-dark text-white border-warning @error('result_volume_m3') is-invalid @enderror" value="{{ old('result_volume_m3') }}" required>
                            @error('result_volume_m3')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-warning">Tanggal Scan <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="scanned_at" class="form-control bg-dark text-white border-warning @error('scanned_at') is-invalid @enderror" value="{{ old('scanned_at', now()->format('Y-m-d\TH:i')) }}" required>
                            @error('scanned_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label text-warning">Catatan (opsional)</label>
                            <textarea name="notes" class="form-control bg-dark text-white border-warning" rows="3">{{ old('notes') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-voltruck w-100"><i class="bi bi-save"></i> Simpan Scan</button>
                    </form>
                    <!-- Modal Scan Plat -->

                </div>
            </div>
        </div>

        <!-- Kolom 2: Visualisasi Point Cloud -->
        <div class="col-md-8">
            <div class="card card-voltruck h-100">
                <div class="card-header bg-transparent border-bottom border-warning">
                    <h5 class="mb-0 text-warning"><i class="bi bi-cube"></i> Point Cloud Muatan</h5>
                </div>
                <div class="card-body p-2">
                    <!-- Baris tombol & info -->
                    <div class="row mb-2 align-items-center">
                        <div class="col-6">
                            <button id="startScanBtn" class="btn btn-voltruck w-100 PY-0"><i class="bi bi-play-fill"></i>Scanner</button>
                        </div>
                        <div class="col-6 text-end">
                            <span class="text-warning"><i class="bi bi-stopwatch"></i> Time: <span id="scanTimer">0.0</span> Dtk</span>
                        </div>
                    </div>
                    <!-- Progress bar -->
                    <div class="progress mb-3" style="height: 8px;">
                        <div id="scanProgress" class="progress-bar bg-warning" role="progressbar" style="width: 0%"></div>
                    </div>
                    <!-- Hasil volume hitung (point cloud) -->
                    <div class="alert alert-dark bg-dark text-warning border-warning mb-2">
                        <i class="bi bi-calculator"></i> Volume hasil hitung dari point cloud: 
                        <strong id="computedVolume">0.00</strong> m³
                    </div>
                    <!-- Canvas -->
                    <div id="pointCloudCanvas" style="width:100%; height:400px; background:#111; border-radius:12px; border:1px solid #ffd700;"></div>
                    <p class="text-center text-white-50 small mt-2">Bentuk muatan berdasarkan dimensi bak truk yang dipilih. Klik "Mulai Scanner" untuk simulasi scanning.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
    <script>
        // Data truk dari controller
        const trucksData = @json($trucksData);

        (function() {
            let scene, camera, renderer, pointsMesh, boundingBox;
            const container = document.getElementById('pointCloudCanvas');
            if (!container) return;

            // Variabel untuk scanning
            let scanning = false;
            let scanInterval = null;
            let startTime = null;
            let pointsCollected = [];
            let currentX = -999;
            let truckLength = 4.2, truckWidth = 2.1, truckHeight = 1.5;
            let scanningStep = 0;
            let totalSteps = 80;

            // Fungsi calculateVolumeFromPoints
            function calculateVolumeFromPoints(points, length, width) {
                if (!points.length) return 0;
                const gridSize = 0.05;
                const xMin = -length/2, xMax = length/2;
                const zMin = -width/2, zMax = width/2;
                const xSteps = Math.ceil((xMax - xMin) / gridSize);
                const zSteps = Math.ceil((zMax - zMin) / gridSize);
                let totalVolume = 0;
                const cellArea = gridSize * gridSize;
                const gridHeights = Array(xSteps).fill().map(() => Array(zSteps).fill().map(() => []));
                for (let p of points) {
                    const xi = Math.floor((p.x - xMin) / gridSize);
                    const zi = Math.floor((p.z - zMin) / gridSize);
                    if (xi >= 0 && xi < xSteps && zi >= 0 && zi < zSteps) {
                        gridHeights[xi][zi].push(p.y);
                    }
                }
                for (let i = 0; i < xSteps; i++) {
                    for (let j = 0; j < zSteps; j++) {
                        const heights = gridHeights[i][j];
                        if (heights.length) {
                            const avgHeight = heights.reduce((a,b) => a+b, 0) / heights.length;
                            totalVolume += cellArea * avgHeight;
                        }
                    }
                }
                return totalVolume;
            }

            // Reset point cloud (hapus semua titik, tampilkan bounding box)
            function resetPointCloud() {
                if (pointsMesh) scene.remove(pointsMesh);
                if (boundingBox) scene.remove(boundingBox);
                pointsCollected = [];
                // Tampilkan bounding box wireframe
                const boxGeo = new THREE.BoxGeometry(truckLength, truckHeight, truckWidth);
                const boxMat = new THREE.MeshBasicMaterial({ color: 0xffaa55, wireframe: true, transparent: true, opacity: 0.4 });
                boundingBox = new THREE.Mesh(boxGeo, boxMat);
                boundingBox.position.set(0, truckHeight/2, 0);
                scene.add(boundingBox);
                // Buat pointsMesh kosong
                const geometry = new THREE.BufferGeometry();
                const pointMaterial = new THREE.PointsMaterial({ size: 0.02, vertexColors: true, blending: THREE.AdditiveBlending });
                pointsMesh = new THREE.Points(geometry, pointMaterial);
                scene.add(pointsMesh);
            }

            // Tambah satu slice di posisi x (dengan lebar slice)
            function addScanSlice(xPos, sliceWidth = 0.05) {
                const halfL = truckLength / 2;
                const halfW = truckWidth / 2;
                const maxZ = truckHeight * 0.95;
                if (xPos < -halfL || xPos > halfL) return;
                const xMin = xPos - sliceWidth/2;
                const xMax = xPos + sliceWidth/2;
                const pointsPerSlice = 80;
                // Ambil array posisi dan warna saat ini
                let positions = pointsMesh.geometry.attributes.position ? Array.from(pointsMesh.geometry.attributes.position.array) : [];
                let colors = pointsMesh.geometry.attributes.color ? Array.from(pointsMesh.geometry.attributes.color.array) : [];
                for (let i = 0; i < pointsPerSlice; i++) {
                    let x = xMin + Math.random() * sliceWidth;
                    let z = (Math.random() - 0.5) * (truckWidth - 0.08);
                    // Hitung tinggi permukaan tidak rata
                    let r = Math.sqrt(x*x + z*z);
                    let maxR = Math.sqrt(halfL*halfL + halfW*halfW);
                    let ratio = Math.min(1, r / maxR);
                    let baseHeight = maxZ - ratio * 0.2 * maxZ;
                    let wave = Math.sin(x * 4.5) * 0.04 + Math.cos(z * 5) * 0.04 + Math.sin(x * 8 + z * 6) * 0.03;
                    let bump = (Math.random() < 0.3) ? 0.05 * Math.random() : 0;
                    let noise = (Math.random() - 0.5) * 0.02;
                    let y = baseHeight + wave + bump + noise;
                    y = Math.max(0.05, Math.min(truckHeight - 0.02, y));
                    positions.push(x, y, z);
                    let intensity = 0.6 + (y / truckHeight) * 0.4;
                    colors.push(1.0, intensity * 0.9, 0.1 + intensity * 0.2);
                    pointsCollected.push({ x, y, z });
                }
                // Update geometry
                const newGeometry = new THREE.BufferGeometry();
                newGeometry.setAttribute('position', new THREE.BufferAttribute(new Float32Array(positions), 3));
                newGeometry.setAttribute('color', new THREE.BufferAttribute(new Float32Array(colors), 3));
                pointsMesh.geometry.dispose();
                pointsMesh.geometry = newGeometry;
            }

            // Fungsi mulai scanning
            function startScanning() {
                if (scanning) return;
                scanning = true;
                // Ambil dimensi truk dari select
                const select = document.getElementById('truckSelect');
                if (select && select.value) {
                    const truck = trucksData.find(t => t.id == select.value);
                    if (truck) {
                        truckLength = truck.length;
                        truckWidth = truck.width;
                        truckHeight = truck.height;
                    }
                }
                resetPointCloud();
                pointsCollected = [];
                const halfL = truckLength / 2;
                currentX = -halfL;
                scanningStep = 0;
                totalSteps = 80;
                const stepSize = truckLength / totalSteps;
                startTime = performance.now();
                document.getElementById('scanProgress').style.width = '0%';
                document.getElementById('scanTimer').innerText = '0.0';
                const startBtn = document.getElementById('startScanBtn');
                startBtn.disabled = true;
                startBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Scanning...';
                
                if (scanInterval) clearInterval(scanInterval);
                scanInterval = setInterval(() => {
                    if (currentX <= halfL) {
                        addScanSlice(currentX, stepSize);
                        currentX += stepSize;
                        scanningStep++;
                        const percent = (scanningStep / totalSteps) * 100;
                        document.getElementById('scanProgress').style.width = percent + '%';
                        const elapsed = (performance.now() - startTime) / 1000;
                        document.getElementById('scanTimer').innerText = elapsed.toFixed(1);
                    } else {
                        clearInterval(scanInterval);
                        scanInterval = null;
                        scanning = false;
                        startBtn.disabled = false;
                        startBtn.innerHTML = '<i class="bi bi-play-fill"></i> Mulai Scanner';
                        const volume = calculateVolumeFromPoints(pointsCollected, truckLength, truckWidth);
                        const volumeDisplay = document.getElementById('computedVolume');
                        volumeDisplay.innerText = volume.toFixed(2);
                        // Isi ke input manual
                        const manualInput = document.getElementById('volumeManualInput');
                        if (manualInput) {
                            manualInput.value = volume.toFixed(2);
                            manualInput.dispatchEvent(new Event('input'));
                        }
                    }
                }, 50);
            }

            // Inisialisasi Three.js
            function initThree() {
                const width = container.clientWidth;
                const height = 400;
                scene = new THREE.Scene();
                scene.background = new THREE.Color(0x111111);
                camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
                camera.position.set(2, 1.5, 3);
                camera.lookAt(0, 0, 0);
                renderer = new THREE.WebGLRenderer({ antialias: true });
                renderer.setSize(width, height);
                container.innerHTML = '';
                container.appendChild(renderer.domElement);
                // Lighting
                const ambientLight = new THREE.AmbientLight(0x404060);
                scene.add(ambientLight);
                const dirLight = new THREE.DirectionalLight(0xffffff, 1);
                dirLight.position.set(1, 2, 1);
                scene.add(dirLight);
                const backLight = new THREE.DirectionalLight(0xffaa66, 0.5);
                backLight.position.set(-1, 1, -1);
                scene.add(backLight);
                const gridHelper = new THREE.GridHelper(4, 20, 0xffd700, 0x444444);
                gridHelper.position.y = -0.5;
                scene.add(gridHelper);
                window.addEventListener('resize', () => {
                    const newWidth = container.clientWidth;
                    const newHeight = 400;
                    renderer.setSize(newWidth, newHeight);
                    camera.aspect = newWidth / newHeight;
                    camera.updateProjectionMatrix();
                });
            }

            function animate() {
                requestAnimationFrame(animate);
                if (pointsMesh) pointsMesh.rotation.y += 0.002;
                if (boundingBox) boundingBox.rotation.y += 0.002;
                if (camera && renderer && scene) {
                    camera.lookAt(0, 0.5, 0);
                    renderer.render(scene, camera);
                }
            }

            document.addEventListener('DOMContentLoaded', function() {
                initThree();
                animate();
                // Inisialisasi bounding box dan titik kosong
                resetPointCloud();
                // Event listener untuk tombol start
                const startBtn = document.getElementById('startScanBtn');
                if (startBtn) startBtn.addEventListener('click', startScanning);
                // Saat pilihan truk berubah, reset visualisasi dengan bounding box baru
                const select = document.getElementById('truckSelect');
                if (select) {
                    select.addEventListener('change', function() {
                        if (scanning) return;
                        const truck = trucksData.find(t => t.id == this.value);
                        if (truck) {
                            truckLength = truck.length;
                            truckWidth = truck.width;
                            truckHeight = truck.height;
                        } else {
                            truckLength = 4.2; truckWidth = 2.1; truckHeight = 1.5;
                        }
                        resetPointCloud();
                        document.getElementById('computedVolume').innerText = '0.00';
                    });
                }
            });
        })();
    </script>

    <!-- Tabel Riwayat -->
    <div class="row mt-3">
        <div class="col-4">
            <div class="card card-voltruck">
                <div class="card-header bg-transparent border-bottom border-warning">
                    <h5 class="mb-0 text-warning"><i class="bi bi-camera"></i> Scan Plat Truk</h5>
                </div>
                <div class="card-body p-2">

                </div>
            </div>
        </div>
            <div class="col-8">
                <div class="card card-voltruck">
                    <div class="card-header bg-transparent border-bottom border-warning">
                        <h5 class="mb-0 text-warning"><i class="bi bi-clock-history"></i> Riwayat Scanning</h5>
                    </div>
                    <div class="card-body p-2">
                        @if(session('success'))
                            <div class="alert alert-success m-3">{{ session('success') }}</div>
                        @endif

                        @if($scanners->count() > 0)
                            <div class="table-responsive">
                                <table class="table table-voltruck table-bordered align-middle mb-0">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th><th>Truk</th><th>Driver</th><th>Tanggal Scan</th><th>Volume (m³)</th><th>Catatan</th><th>Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($scanners as $item)
                                        <tr>
                                            <td>{{ $item->id }}</td>
                                            <td>{{ $item->truck->plate_number ?? '-' }} - {{ $item->truck->name ?? '' }}</td>
                                            <td>{{ $item->driver->name ?? '-' }}</td>
                                            <td>{{ $item->scanned_at ? $item->scanned_at->format('d/m/Y H:i') : '-' }}</td>
                                            <td><span class="badge bg-warning text-dark px-3 py-2 rounded-pill">{{ number_format($item->result_volume_m3, 2) }}</span></td>
                                            <td>{{ $item->notes ?? '-' }}</td>
                                            <td>
                                                <form action="{{ route('scanner.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin hapus data ini?')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i> Hapus</button>
                                                </form>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="d-flex justify-content-center mt-3 pb-3">
                                {{ $scanners->links('pagination::bootstrap-5') }}
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="bi bi-database-slash fs-1 text-muted"></i>
                                <p class="mt-2">Belum ada data scanning.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/three.js/r128/three.min.js"></script>
<script>
    // Data truk dari controller (sudah dalam bentuk array JavaScript)
    const trucksData = @json($trucksData);

    (function() {
        let scene, camera, renderer, pointsMesh, boundingBox;
        const container = document.getElementById('pointCloudCanvas');

        if (!container) return;

        function initThree() {
            const width = container.clientWidth;
            const height = 400;

            scene = new THREE.Scene();
            scene.background = new THREE.Color(0x111111);

            camera = new THREE.PerspectiveCamera(45, width / height, 0.1, 1000);
            camera.position.set(2, 1.5, 3);
            camera.lookAt(0, 0, 0);

            renderer = new THREE.WebGLRenderer({ antialias: true });
            renderer.setSize(width, height);
            container.innerHTML = '';
            container.appendChild(renderer.domElement);

            // Lighting
            const ambientLight = new THREE.AmbientLight(0x404060);
            scene.add(ambientLight);
            const dirLight = new THREE.DirectionalLight(0xffffff, 1);
            dirLight.position.set(1, 2, 1);
            scene.add(dirLight);
            const backLight = new THREE.DirectionalLight(0xffaa66, 0.5);
            backLight.position.set(-1, 1, -1);
            scene.add(backLight);

            const gridHelper = new THREE.GridHelper(4, 20, 0xffd700, 0x444444);
            gridHelper.position.y = -0.5;
            scene.add(gridHelper);

            window.addEventListener('resize', () => {
                const newWidth = container.clientWidth;
                const newHeight = 400;
                renderer.setSize(newWidth, newHeight);
                camera.aspect = newWidth / newHeight;
                camera.updateProjectionMatrix();
            });
        }

        // Fungsi untuk menghasilkan point cloud bak truk dengan muatan tidak rata
        function generatePointCloud(length, width, height) {
            if (pointsMesh) scene.remove(pointsMesh);
            if (boundingBox) scene.remove(boundingBox);

            const halfL = length / 2;
            const halfW = width / 2;
            const maxMaterialZ = height * 0.95;
            
            const positions = [];
            const colors = [];
            const materialPointsList = []; // untuk menyimpan titik muatan (x,y,z)
            
            // 1. Lantai (dasar)
            const floorPointsCount = 600;
            for (let i = 0; i < floorPointsCount; i++) {
                let x = (Math.random() - 0.5) * (length - 0.1);
                let z = (Math.random() - 0.5) * (width - 0.1);
                let y = -0.03;
                positions.push(x, y, z);
                colors.push(0.25, 0.25, 0.35);
            }
            
            // 2. Dinding
            const wallPointsCount = 800;
            const wallHeight = height;
            for (let i = 0; i < wallPointsCount; i++) {
                let side = Math.floor(Math.random() * 4);
                let x, z;
                const margin = 0.02;
                if (side === 0) { x = -halfL + margin; z = (Math.random() - 0.5) * width; }
                else if (side === 1) { x = halfL - margin; z = (Math.random() - 0.5) * width; }
                else if (side === 2) { x = (Math.random() - 0.5) * length; z = -halfW + margin; }
                else { x = (Math.random() - 0.5) * length; z = halfW - margin; }
                let y = Math.random() * wallHeight;
                positions.push(x, y, z);
                colors.push(0.85, 0.75, 0.25);
            }
            
            // 3. Muatan (titik padat dengan permukaan tidak rata)
            const materialPointsCount = 5000;
            for (let i = 0; i < materialPointsCount; i++) {
                let x = (Math.random() - 0.5) * (length - 0.08);
                let z = (Math.random() - 0.5) * (width - 0.08);
                let r = Math.sqrt(x*x + z*z);
                let maxR = Math.sqrt(halfL*halfL + halfW*halfW);
                let ratio = r / maxR;
                let baseHeight = maxMaterialZ - ratio * 0.2 * maxMaterialZ;
                let wave = Math.sin(x * 4.5) * 0.04 + Math.cos(z * 5) * 0.04 + Math.sin(x * 8 + z * 6) * 0.03;
                let bump = (Math.random() < 0.3) ? 0.05 * Math.random() : 0;
                let noise = (Math.random() - 0.5) * 0.02;
                let y = baseHeight + wave + bump + noise;
                y = Math.max(0.05, Math.min(height - 0.02, y));
                positions.push(x, y, z);
                materialPointsList.push({ x, y, z });
                let intensity = 0.6 + (y / height) * 0.4;
                colors.push(1.0, intensity * 0.9, 0.1 + intensity * 0.2);
            }
            
            // Hitung volume dari materialPointsList
            let computedVolume = 0;
            if (materialPointsList.length) {
                computedVolume = calculateVolumeFromPoints(materialPointsList, length, width);
                const volumeDisplay = document.getElementById('computedVolume');
                if (volumeDisplay) {
                    volumeDisplay.innerText = computedVolume.toFixed(2);
                    // Bandingkan dengan input manual jika ada
                    const manualInput = document.querySelector('input[name="result_volume_m3"]');
                    if (manualInput && manualInput.value) {
                        const manualVal = parseFloat(manualInput.value);
                        if (!isNaN(manualVal)) {
                            volumeDisplay.innerHTML = `${computedVolume.toFixed(2)} m³ <span class="text-muted">(deviasi ${Math.abs(computedVolume - manualVal).toFixed(2)} m³)</span>`;
                        }
                    }
                }
            }
            
            // Buat geometry dan tampilkan
            const geometry = new THREE.BufferGeometry();
            geometry.setAttribute('position', new THREE.BufferAttribute(new Float32Array(positions), 3));
            geometry.setAttribute('color', new THREE.BufferAttribute(new Float32Array(colors), 3));
            const pointMaterial = new THREE.PointsMaterial({ size: 0.02, vertexColors: true, blending: THREE.AdditiveBlending });
            pointsMesh = new THREE.Points(geometry, pointMaterial);
            scene.add(pointsMesh);
            
            const boxGeo = new THREE.BoxGeometry(length, height, width);
            const boxMat = new THREE.MeshBasicMaterial({ color: 0xffaa55, wireframe: true, transparent: true, opacity: 0.4 });
            boundingBox = new THREE.Mesh(boxGeo, boxMat);
            boundingBox.position.set(0, height/2, 0);
            scene.add(boundingBox);
        }

        function calculateVolumeFromPoints(points, length, width) {
            if (!points.length) return 0;
            const gridSize = 0.05; // meter per sel
            const xMin = -length/2, xMax = length/2;
            const zMin = -width/2, zMax = width/2;
            const xSteps = Math.ceil((xMax - xMin) / gridSize);
            const zSteps = Math.ceil((zMax - zMin) / gridSize);
            let totalVolume = 0;
            const cellArea = gridSize * gridSize;
            // Inisialisasi grid untuk menyimpan daftar tinggi tiap sel
            const gridHeights = Array(xSteps).fill().map(() => Array(zSteps).fill().map(() => []));
            for (let p of points) {
                const xi = Math.floor((p.x - xMin) / gridSize);
                const zi = Math.floor((p.z - zMin) / gridSize);
                if (xi >= 0 && xi < xSteps && zi >= 0 && zi < zSteps) {
                    gridHeights[xi][zi].push(p.y);
                }
            }
            for (let i = 0; i < xSteps; i++) {
                for (let j = 0; j < zSteps; j++) {
                    const heights = gridHeights[i][j];
                    if (heights.length) {
                        const avgHeight = heights.reduce((a,b) => a+b, 0) / heights.length;
                        totalVolume += cellArea * avgHeight;
                    }
                }
            }
            return totalVolume;
        }

        function animate() {
            requestAnimationFrame(animate);
            if (pointsMesh) pointsMesh.rotation.y += 0.002;
            if (boundingBox) boundingBox.rotation.y += 0.002;
            if (camera && renderer && scene) {
                camera.lookAt(0, 0.5, 0);
                renderer.render(scene, camera);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            initThree();
            animate();

            const select = document.getElementById('truckSelect');
            if (!select) return;

            function updateVisualization(truckId) {
                const truck = trucksData.find(t => t.id == truckId);
                if (truck) {
                    generatePointCloud(truck.length, truck.width, truck.height);
                } else {
                    generatePointCloud(4.2, 2.1, 1.5);
                }
            }

            if (select.value) {
                updateVisualization(select.value);
            } else {
                generatePointCloud(4.2, 2.1, 1.5);
            }

            select.addEventListener('change', function() {
                updateVisualization(this.value);
            });
        });
    })();
</script>

<!-- CAMERA SCANN-->
<!-- Modal Scan Plat (tanpa overlay rumit dulu) -->
<div class="modal fade" id="scanPlateModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-warning">
            <div class="modal-header border-bottom border-warning">
                <h5 class="modal-title text-warning"><i class="bi bi-camera-fill"></i> Scan Plat Truk</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cameraStatus" class="alert alert-warning">Meminta akses kamera...</div>
                <video id="scanVideo" width="100%" autoplay muted playsinline style="border-radius: 8px; background: #000;"></video>
                <div class="mt-3">
                    <button id="capturePlateBtn" class="btn btn-voltruck w-100" disabled><i class="bi bi-camera-fill"></i> Ambil Gambar</button>
                </div>
                <div class="mt-2">
                    <label class="form-label text-warning small">Hasil Scan:</label>
                    <input type="text" id="scannedPlateResult" class="form-control bg-dark text-white border-warning" placeholder="Plat akan muncul di sini" readonly>
                </div>
            </div>
            <div class="modal-footer border-top border-warning">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                <button type="button" id="applyScannedPlate" class="btn btn-voltruck" disabled>Gunakan Plat Ini</button>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('scanPlateModal');
        const video = document.getElementById('scanVideo');
        const statusDiv = document.getElementById('cameraStatus');
        const captureBtn = document.getElementById('capturePlateBtn');
        const resultInput = document.getElementById('scannedPlateResult');
        const applyBtn = document.getElementById('applyScannedPlate');
        let stream = null;
        let currentImageData = null;

        // Helper show status
        function setStatus(msg, isError = false) {
            statusDiv.innerHTML = msg;
            statusDiv.className = `alert ${isError ? 'alert-danger' : 'alert-info'}`;
        }

        // Mulai kamera saat modal dibuka
        modal.addEventListener('show.bs.modal', async function() {
            setStatus('Mengakses kamera... pastikan izin diberikan.', false);
            try {
                // Gunakan constraint sederhana (kamera belakang jika ada)
                const mediaStream = await navigator.mediaDevices.getUserMedia({
                    video: { facingMode: "environment" }
                });
                stream = mediaStream;
                video.srcObject = stream;
                await video.play();
                setStatus('Kamera aktif. Arahkan ke plat lalu tekan tombol "Ambil Gambar".', false);
                captureBtn.disabled = false;
            } catch (err) {
                console.error(err);
                setStatus('Gagal akses kamera: ' + err.message, true);
                captureBtn.disabled = true;
            }
        });

        // Hentikan kamera saat modal ditutup
        modal.addEventListener('hide.bs.modal', function() {
            if (stream) {
                stream.getTracks().forEach(track => track.stop());
                video.srcObject = null;
            }
            resultInput.value = '';
            applyBtn.disabled = true;
        });

        // Capture gambar dan OCR
        captureBtn.addEventListener('click', async function() {
            if (!video.videoWidth) {
                alert('Kamera belum siap. Tunggu sebentar.');
                return;
            }
            captureBtn.disabled = true;
            captureBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Memproses...';
            // Buat canvas untuk mengambil gambar dari video
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            // Optional: crop area tengah (misal 70% width, 20% height) untuk fokus pada plat
            const cropX = canvas.width * 0.15;
            const cropY = canvas.height * 0.4;
            const cropW = canvas.width * 0.7;
            const cropH = canvas.height * 0.2;
            const cropCanvas = document.createElement('canvas');
            cropCanvas.width = cropW;
            cropCanvas.height = cropH;
            const cropCtx = cropCanvas.getContext('2d');
            cropCtx.drawImage(canvas, cropX, cropY, cropW, cropH, 0, 0, cropW, cropH);
            // Preprocessing sederhana (grayscale + threshold)
            const imageData = cropCtx.getImageData(0, 0, cropW, cropH);
            const processed = preprocessImage(imageData, cropW, cropH);
            cropCtx.putImageData(processed, 0, 0);
            // OCR
            try {
                const { data: { text } } = await Tesseract.recognize(cropCanvas, 'eng', {
                    tessedit_char_whitelist: 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789 ',
                    tessedit_pageseg_mode: 7,
                });
                let raw = text.trim().toUpperCase();
                // Regex untuk plat Indonesia (contoh: B 1234 XYZ, B 1234AB)
                let plateMatch = raw.match(/[A-Z]{1,2}\s?\d{1,4}\s?[A-Z]{0,3}/);
                let plate = plateMatch ? plateMatch[0].replace(/\s+/g, ' ').trim() : '';
                if (plate) {
                    resultInput.value = plate;
                    applyBtn.disabled = false;
                    setStatus('Plat terbaca: ' + plate, false);
                } else {
                    resultInput.value = 'Tidak terbaca, coba lagi';
                    applyBtn.disabled = true;
                    setStatus('Gagal baca plat. Pastikan plat dalam frame dan cukup cahaya.', true);
                }
            } catch (err) {
                console.error(err);
                resultInput.value = 'Error OCR';
                setStatus('Terjadi kesalahan saat memproses gambar.', true);
            } finally {
                captureBtn.disabled = false;
                captureBtn.innerHTML = '<i class="bi bi-camera-fill"></i> Ambil Gambar';
            }
        });

        // Preprocessing (grayscale + threshold)
        function preprocessImage(imageData, w, h) {
            const gray = new Uint8ClampedArray(w * h);
            for (let i = 0; i < imageData.data.length; i += 4) {
                let lum = 0.299 * imageData.data[i] + 0.587 * imageData.data[i+1] + 0.114 * imageData.data[i+2];
                gray[i/4] = lum;
            }
            const threshold = 100;
            const output = new ImageData(w, h);
            for (let i = 0; i < gray.length; i++) {
                let val = gray[i] < threshold ? 0 : 255;
                output.data[i*4] = val;
                output.data[i*4+1] = val;
                output.data[i*4+2] = val;
                output.data[i*4+3] = 255;
            }
            return output;
        }

        // Gunakan hasil plat untuk memilih truk
        applyBtn.addEventListener('click', function() {
            const scannedPlate = resultInput.value;
            if (scannedPlate && !scannedPlate.includes('Tidak')) {
                const truckSelect = document.getElementById('truckSelect');
                if (truckSelect) {
                    let found = false;
                    for (let i = 0; i < truckSelect.options.length; i++) {
                        if (truckSelect.options[i].text.includes(scannedPlate)) {
                            truckSelect.value = truckSelect.options[i].value;
                            truckSelect.dispatchEvent(new Event('change'));
                            found = true;
                            break;
                        }
                    }
                    if (!found) {
                        const notes = document.querySelector('textarea[name="notes"]');
                        if (notes) {
                            let current = notes.value;
                            notes.value = current ? current + '\n' + 'Plat: ' + scannedPlate : 'Plat: ' + scannedPlate;
                        }
                        alert('Plat "' + scannedPlate + '" tidak ditemukan. Ditambahkan ke catatan.');
                    } else {
                        alert('Truk dengan plat "' + scannedPlate + '" berhasil dipilih.');
                    }
                }
                // Tutup modal
                bootstrap.Modal.getInstance(modal).hide();
            }
        });
    });
</script>


@endsection