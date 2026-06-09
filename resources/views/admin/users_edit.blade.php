@extends('layouts.app')

@section('title', 'Edit User')

@section('content')
<div class="container-fluid mt-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-warning mb-0 mt-0">✏️ Edit User: {{ $user->name }}</h6>
        <a href="{{ route('admin.users') }}" class="btn btn-outline-warning btn-sm py-0">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="row g-4">
        <!-- Kolom 1: Card Icon User -->
        <div class="col-md-4">
            <div class="card card-voltruck">
                <div class="card-header bg-transparent border-bottom border-warning">
                    <h5 class="mb-0 text-warning"><i class="bi bi-person-badge"></i> Profil User</h5>
                </div>
                <div class="card-body text-center">
                    <div class="avatar-circle mx-auto mb-2">
                        <i class="bi bi-person-circle text-warning" style="font-size: 7rem;"></i>
                    </div>
                    <h5 class="text-white">{{ $user->name }}</h5>
                    <p class="text-white-50">{{ $user->email }}</p>
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill">
                        {{ ucfirst($user->role) }}
                    </span>
                    <hr class="bg-warning">
                    <div class="text-start text-white-50">
                        <small><i class="bi bi-calendar3"></i> Bergabung: {{ $user->created_at->format('d/m/Y') }}</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom 2: Form Edit User -->
        <div class="col-md-8">
            <div class="card card-voltruck">
                <div class="card-header bg-transparent border-bottom border-warning">
                    <h5 class="mb-0 text-warning"><i class="bi bi-pencil-square"></i> Edit Data User</h5>
                </div>
                <div class="card-body col-10">
                    <form action="{{ route('admin.users.update', $user->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label class="form-label text-warning">Nama</label>
                            <input type="text" name="name" class="form-control bg-dark text-white border-warning @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-warning">Email</label>
                            <input type="email" name="email" class="form-control bg-dark text-white border-warning @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label text-warning">Role</label>
                            <select name="role" class="form-select bg-dark text-white border-warning @error('role') is-invalid @enderror" required>
                                <option value="user" {{ $user->role == 'user' ? 'selected' : '' }}>User</option>
                                <option value="admin" {{ $user->role == 'admin' ? 'selected' : '' }}>Admin</option>
                            </select>
                            @error('role')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <button type="submit" class="btn btn-voltruck">
                                <i class="bi bi-save"></i> Update User
                            </button>
                            <a href="{{ route('admin.users') }}" class="btn btn-outline-warning">
                                <i class="bi bi-arrow-left"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection