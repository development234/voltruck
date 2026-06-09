@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container-fluid mt-0 px-0" >
    <div class="row justify-content-center">
        <div class="col-lg-4">
            <div class="card card-voltruck border-1 shadow-lg h-100" style="min-height: 30rem;">
                <div class="card-header bg-transparent border-bottom border-warning">
                    <h5 class="mb-0 text-warning"><i class="bi bi-person-circle text-primary"></i> Profil Operator</h5>
                </div>
                <div class="card-body p-4">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- 2 KOLOM: Avatar + Informasi -->
                    <div class="row align-items-center g-4 mb-4">
                        <!-- Kolom kiri: Icon / Avatar -->
                        <div class="col-md-4 text-center">
                            <div class="avatar-wrapper mx-auto" style="width: 130px; height: 130px;">
                                <div class="rounded-circle  d-flex align-items-center justify-content-center h-100 w-100 border border-warning border-2 shadow">
                                    <i class="bi bi-person-fill text-warning" style="font-size: 6rem;"></i>
                                </div>
                            </div>
                            <!--<div class="mt-3">
                                <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">{{ ucfirst($user->role) }}</span>
                            </div>-->
     
                                <div class="mt-2">
                                    <span class="px-3 py-0 text-align-center rounded-pill">
                                        @if($user->role == 'user')
                                            <h5 class="badge bg-warning fw-bold text-secondary">OPERATOR</h5>
                                        @elseif($user->role == 'admin')
                                            <h5 class="badge bg-info fw-bold text-dark">ADMINISTRATOR</h5>
                                        @else
                                            {{ ucfirst($user->role) }}
                                        @endif
                                    </span>
                                </div>
                            
                        </div>

                        <!-- Kolom kanan: Informasi User -->
                        <div class="col-md-8">
                            <div class="info-grid">
                                <div class="row mb-2">
                                    <div class="col-5 text-warning fw-semibold">Nama Lengkap</div>
                                    <div class="col-7 text-white">{{ $user->name }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 text-warning fw-semibold">Email</div>
                                    <div class="col-7 text-white">{{ $user->email }}</div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 text-warning fw-semibold">Role</div>
                                    <div class="col-7"><span class="text-capitalize text-info">{{ $user->role }}</span></div>
                                </div>
                                <div class="row mb-2">
                                    <div class="col-5 text-warning fw-semibold">Mulai Gabung</div>
                                    <div class="col-7 text-white-50">{{ $user->created_at->translatedFormat('d F Y') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer border-top border-warning">
                    <!-- Tombol Aksi -->
                    <div class="d-flex justify-content-between text-center flex-wrap gap-2 mt-3">
                        <button type="button" class="btn btn-voltruck py-0" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="bi bi-pencil-square"></i> Edit Profil
                        </button>
                        <button type="button" class="btn btn-outline-danger py-0" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                            <i class="bi bi-trash3"></i> Hapus Akun
                        </button>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card card-voltruck border-1 shadow-lg h-100" style="min-height: 30rem;">
                <div class="card-header bg-transparent border-bottom border-warning">
                    <h5 class="mb-0 text-warning"><i class="bi bi-clock-history"></i> Riwayat Pengukuran</h5>
                </div>
<div class="card-body p-4">
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
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-4">
            <i class="bi bi-inbox fs-1 text-muted"></i>
            <p class="mt-2 mb-0">Belum ada riwayat scanning. Silakan lakukan scanning truk.</p>
        </div>
    @endif
</div>
<div class="card-footer border-top border-warning">
    <div class="d-flex justify-content-center">
        {{ $scanners->links('pagination::bootstrap-5') }}
    </div>
</div>
            </div>
        </div>
    </div>
</div>

<!-- MODAL EDIT PROFIL -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border border-warning">
            <div class="modal-header border-bottom border-warning">
                <h5 class="modal-title text-warning" id="editProfileModalLabel">
                    <i class="bi bi-pencil-square"></i> Edit Profil
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label text-warning">Nama Lengkap</label>
                        <input type="text" name="name" class="form-control bg-dark text-white border-warning @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-warning">Email</label>
                        <input type="email" name="email" class="form-control bg-dark text-white border-warning @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-warning">Password Baru (opsional)</label>
                        <input type="password" name="password" class="form-control bg-dark text-white border-warning @error('password') is-invalid @enderror">
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-warning">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" class="form-control bg-dark text-white border-warning">
                    </div>
                </div>
                <div class="modal-footer border-top border-warning">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-voltruck">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- MODAL HAPUS AKUN (konfirmasi) -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content bg-dark text-white border border-danger">
            <div class="modal-header border-bottom border-danger">
                <h5 class="modal-title text-danger" id="deleteAccountModalLabel">Hapus Akun</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Apakah Anda yakin ingin menghapus akun <strong class="text-warning">{{ $user->name }}</strong>?</p>
                <p class="text-danger-emphasis small">Semua data pengukuran Anda akan ikut terhapus. Tindakan ini tidak dapat dibatalkan.</p>
            </div>
            <div class="modal-footer border-top border-danger">
                <form action="{{ route('profile.destroy') }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus Akun</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection