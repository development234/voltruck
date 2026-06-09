@extends('layouts.app')

@section('title', 'Login - VolTruck')

@section('content')
<div class="row justify-content-center align-items-center min-vh-100">
    <div class="col-md-5 col-lg-4">
        <div class="card login-card">
            <div class="card-body p-4 p-md-5">
                <div class="text-center mb-3">
                    <div class="icon-circle border-1" style="width:60px;height:60px">
                        <i class="bi bi-truck-front-fill fs-1 text-warning"></i>
                    </div>
                </div>
                <div class="text-center mb-3">
                    <h3 class="text-warning">Vol<span class="text-danger">Truck</span></h3>
                    <p class="text-white-50">Sistem Ukur Volume Muatan Truk</p>
                </div>

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ $errors->first() }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" novalidate>
                    @csrf
                    <div class="mb-3">
                        <label class="form-label text-warning">Email Address</label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required autofocus>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label text-warning">Password</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <button type="submit" class="btn btn-voltruck w-100">Login</button>
                </form>

                <div class="mt-4 text-center text-white-50 small">
                    Demo: <strong>user@voltruck.com</strong> / user123 &nbsp;|&nbsp;
                    <strong>admin@voltruck.com</strong> / admin123
                </div>
            </div>
        </div>
    </div>
</div>
@endsection