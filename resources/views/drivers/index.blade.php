@extends('layouts.app')

@section('title', 'Manajemen Pengemudi')

@section('content')
<div class="container-fluid mt-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-warning">👨‍✈️ Daftar Pengemudi</h6>
        <a href="{{ route('drivers.create') }}" class="btn btn-voltruck py-0">
            <i class="bi bi-plus-circle fw-bold"></i> Driver
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-voltruck h-100">
        <div class="card-header">📋 Data Pengemudi</div>
        <div class="card-body">
            @if($drivers->count() > 0)
                <div class="table-responsive">
                    <table class="table table-voltruck table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Nama Pengemudi</th>
                                <th>Nomor Lisensi</th>
                                <th>Nomor Telepon</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($drivers as $index => $driver)
                            <tr>
                                <td>{{ $drivers->firstItem() + $index }}</td>
                                <td>{{ $driver->name }}</td>
                                <td>{{ $driver->license_number ?? '-' }}</td>
                                <td>{{ $driver->phone ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('drivers.edit', $driver->id) }}" class="btn btn-sm btn-outline-info py-0">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <form action="{{ route('drivers.destroy', $driver->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Yakin hapus pengemudi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                 </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination rapi & kecil -->
                <div class="d-flex justify-content-center mt-3">
                    {{ $drivers->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-people fs-1 text-muted"></i>
                    <p class="mt-2 mb-0">Belum ada data pengemudi.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection