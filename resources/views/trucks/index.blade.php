@extends('layouts.app')

@section('title', 'Manajemen Truk')

@section('content')
<div class="container-fluid mt-0">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="text-warning fw-bold"><i class="bi bi-truck-front-fill fs-6"></i> Daftar Truk</h6>
        <a href="{{ route('trucks.create') }}" class="btn btn-sm py-0 btn-voltruck fw-bold">
            <i class="bi bi-plus-circle"></i> Tambah Truk
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card card-voltruck">
        <div class="card-header fw-bold"><i class="bi bi-truck-front fs-7"></i> Data Truk</div>
        <div class="card-body">
            @if($trucks->count() > 0)
                <div class="table-responsive">
                    <table class="table table-voltruck table-bordered align-middle">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>Plat Nomor</th>
                                <th>Nama Truk</th>
                                <th>Model</th>
                                <th>Ukuran (P x L x T)</th>
                                <th>Isi(m³)</th>
                                <th class='5%'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trucks as $index => $truck)
                            <tr>
                                <td>{{ $trucks->firstItem() + $index }}</td>
                                <td>{{ $truck->plate_number }}</td>
                                <td>{{ $truck->name ?? '-' }}</td>
                                <td>{{ $truck->model ?? '-' }}</td>
                                <td>{{ $truck->length_m }} x {{ $truck->width_m }} x {{ $truck->height_m }}</td>
                                <td>{{ $truck->capacity_m3 ?? '-' }}</td>
                                <td>
                                    <a href="{{ route('trucks.edit', $truck->id) }}" class="btn btn-sm btn-outline-info  py-0">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('trucks.destroy', $truck->id) }}" method="POST" class="d-inline delete-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger py-0" onclick="return confirm('Yakin hapus truk ini?')">
                                            <i class="bi bi-trash"></i>
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
                    {{ $trucks->onEachSide(1)->links('pagination::bootstrap-5') }}
                </div>
            @else
                <div class="text-center py-4">
                    <i class="bi bi-truck fs-1 text-muted"></i>
                    <p class="mt-2 mb-0 fst-italy text-muted "><i class="bi bi-person-circle fs-4"></i>Belum ada data truk. Silakan tambah truk pertama.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection