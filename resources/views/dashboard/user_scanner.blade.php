@extends('layouts.app')

@section('title', 'Riwayat Scanner Saya')

@section('content')
<div class="container-fluid mt-1">
    <div class="card card-voltruck">
        <div class="card-header bg-transparent border-bottom border-warning">
            <h5 class="mb-0 text-warning"><i class="bi bi-qr-code-scan"></i> Riwayat Scanning Saya</h5>
        </div>
        <div class="card-body">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if($scanners->count() > 0)
                <div class="table-responsive">
                    <table class="table table-voltruck table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>Tanggal Scan</th>
                                <th>Truk</th>
                                <th>Driver</th>
                                <th>Volume (m³)</th>
                                <th>Catatan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($scanners as $scan)
                            <tr>
                                <td>{{ $scan->scanned_at ? $scan->scanned_at->format('d/m/Y H:i') : '-' }}</td>
                                <td>{{ $scan->truck->plate_number ?? '-' }} - {{ $scan->truck->name ?? '' }}</td>
                                <td>{{ $scan->driver->name ?? '-' }}</td>
                                <td>
                                    <span class="badge bg-warning text-dark px-3 py-2 rounded-pill">
                                        {{ number_format($scan->result_volume_m3, 2) }}
                                    </span>
                                </td>
                                <td>{{ $scan->notes ?? '-' }}</td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-info" data-bs-toggle="modal" data-bs-target="#viewModal{{ $scan->id }}">
                                        <i class="bi bi-eye"></i> View
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $scan->id }}">
                                        <i class="bi bi-trash"></i> Hapus
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center mt-3">
                    {{ $scanners->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-inbox fs-1 text-muted"></i>
                    <p class="mt-2 mb-0">Belum ada riwayat scanning. Silakan lakukan scanning truk melalui menu Scanner.</p>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal View Detail dengan Cetak Profesional -->
@foreach($scanners as $scan)
<div class="modal fade" id="viewModal{{ $scan->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bg-dark text-white border-warning">
            <div class="modal-header border-bottom border-warning">
                <h5 class="modal-title text-warning"><i class="bi bi-info-circle"></i> Detail Scanner</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="printArea{{ $scan->id }}">
                <!-- Kop surat dan logo akan ditambahkan oleh script cetak -->
                <!-- Informasi detail scanner -->
                <table class="table table-borderless text-white" style="width:100%">
                    <tr><th class="text-warning text-start" style="width:35%">ID Scanner:</th><td>{{ $scan->id }}</td></tr>
                    <tr><th class="text-warning text-start">Tanggal & Waktu Scan:</th><td>{{ $scan->scanned_at ? $scan->scanned_at->format('d/m/Y H:i:s') : '-' }}</td></tr>
                    <tr><th class="text-warning text-start">Truk:</th><td>{{ $scan->truck->plate_number ?? '-' }} - {{ $scan->truck->name ?? '' }}</td></tr>
                    <tr><th class="text-warning text-start">Driver:</th><td>{{ $scan->driver->name ?? '-' }}</td></tr>
                    <tr><th class="text-warning text-start">Volume Hasil Scan (m³):</th><td><strong>{{ number_format($scan->result_volume_m3, 2) }}</strong></td></tr>
                    <tr><th class="text-warning text-start">Catatan:</th><td>{{ $scan->notes ?? '-' }}</td></tr>
                    <tr><th class="text-warning text-start">Operator:</th><td>{{ Auth::user()->name }}</td></tr>
                </table>

                <!-- Tanda Tangan -->
                <div class="mt-4 pt-3 border-top border-secondary">
                    <div class="row justify-content-center">
                        <div class="col-5 text-center">
                            <span class="small text-white-50">Mengetahui,</span><br>
                            <span class="fw-bold">Supervisor</span><br>
                            <br><br>
                            <span class="small">(_____________)</span>
                        </div>
                        <div class="col-5 text-center">
                            <span class="small text-white-50">Operator,</span><br>
                            <span class="fw-bold">{{ Auth::user()->name }}</span><br>
                            <br><br>
                            <span class="small">(_____________)</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-warning">
                <button type="button" class="btn btn-voltruck" onclick="printLaporan({{ $scan->id }})">
                    <i class="bi bi-printer"></i> Cetak Laporan
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Konfirmasi Hapus -->
<div class="modal fade" id="deleteModal{{ $scan->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border-danger">
            <div class="modal-header border-bottom border-danger">
                <h5 class="modal-title text-danger"><i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus data scanner dengan ID <strong>{{ $scan->id }}</strong>?</p>
                <p class="text-danger">Data ini tidak dapat dipulihkan kembali.</p>
            </div>
            <div class="modal-footer border-top border-danger">
                <form action="{{ route('dashboard.user.scanner.destroy', $scan->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function printLaporan(id) {
        // Ambil konten dari area cetak
        const content = document.getElementById(`printArea${id}`).cloneNode(true);
        
        // Sembunyikan elemen yang tidak perlu di cetak (misalnya border-top, bisa dipertahankan)
        // Kita akan membuat kop surat dinamis di halaman cetak
        
        // Data untuk kop surat
        const now = new Date();
        const tanggalCetak = now.toLocaleDateString('id-ID', { day: '2-digit', month: '2-digit', year: 'numeric' });
        const waktuCetak = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
        const noDokumen = 'VOL/SCN/' + now.getFullYear() + '/' + now.getMonth() + '/' + id;
        
        // Buat jendela cetak
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>Laporan Scanner - VolTruck</title>
                <meta charset="UTF-8">
                <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
                <style>
                    @media print {
                        body { margin: 0; padding: 15px; }
                        .page-break { page-break-after: avoid; }
                        .no-print { display: none; }
                    }
                    body {
                        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                        padding: 20px;
                        color: #000;
                        background: #fff;
                    }
                    .header {
                        text-align: center;
                        margin-bottom: 20px;
                        border-bottom: 2px solid #f0ad4e;
                        padding-bottom: 10px;
                    }
                    .logo {
                        max-height: 80px;
                        margin-bottom: 10px;
                    }
                    .company-name {
                        font-size: 24px;
                        font-weight: bold;
                        color: #d9534f;
                    }
                    .company-sub {
                        font-size: 12px;
                        color: #666;
                    }
                    .report-title {
                        font-size: 18px;
                        font-weight: bold;
                        margin: 20px 0;
                        text-align: center;
                        text-transform: uppercase;
                    }
                    .info-dokumen {
                        font-size: 12px;
                        margin-bottom: 20px;
                        text-align: right;
                    }
                    table {
                        width: 100%;
                        border-collapse: collapse;
                        margin: 15px 0;
                    }
                    table tr td, table tr th {
                        padding: 8px;
                        border: 1px solid #ddd;
                    }
                    .ttd {
                        margin-top: 40px;
                    }
                    .footer {
                        margin-top: 30px;
                        text-align: center;
                        font-size: 10px;
                        color: #888;
                        border-top: 1px solid #eee;
                        padding-top: 10px;
                    }
                </style>
            </head>
            <body>
                <div class="header">
                    <!-- Logo VolTruck (gunakan font icon atau gambar) -->
                    <div class="company-name">🚛 Vol<span style="color:#d9534f;">Truck</span></div>
                    <div class="company-sub">Sistem Ukur Volume Muatan Truk</div>
                    <div class="company-sub">Jl. Wonosari KM 10 , Yogyakarta</div>
                </div>
                <div class="report-title">
                    LAPORAN HASIL SCANNING TRUK
                </div>
                <div class="info-dokumen">
                    No. Dokumen: ${noDokumen}<br>
                    Tanggal Cetak: ${tanggalCetak} ${waktuCetak}
                </div>
                
                <!-- Konten data scanner -->
                <div style='font-size:0.9rem'>
                    ${content.outerHTML}
                </div>
                
                <div class="footer">
                    Laporan ini dibuat secara elektronik oleh sistem VolTruck.<br>
                    Dokumen sah tanpa tanda tangan basah.
                </div>
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.print();
    }
</script>
@endforeach
@endsection