@extends('layouts.admin')

@section('title', 'Detail User')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Detail User</h1>
    </div>

    <div class="row">
        <!-- Profile Card: kolom auto (pas konten) -->
        <div class="col-auto mb-4">
            <div class="card profile-card shadow-sm">
                <div class="card-body text-center">
                    <div class="user-avatar mb-3">
                        {{ strtoupper(substr($user->name ?? 'U', 0, 2)) }}
                    </div>
                    <h5 class="card-title mb-1">{{ $user->name ?? 'Tidak ada nama' }}</h5>
                    <p class="text-muted small">{{ '@' . ($user->username ?? 'username') }}</p>
                    <span class="badge badge-success">Active User</span>
                </div>
            </div>
        </div>

        <!-- Informasi User: kolom fleksibel -->
        <div class="col mb-4">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi User</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- Kolom Kiri -->
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="small text-muted">Email</label>
                                <div class="font-weight-bold">{{ $user->email ?? '-' }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="small text-muted">No. Handphone</label>
                                <div class="font-weight-bold">{{ $user->phone_number ?? '-' }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="small text-muted">Jenjang Pendidikan</label>
                                <div class="font-weight-bold">{{ $user->jenjang ?? '-' }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="small text-muted">Tanggal Daftar</label>
                                <div class="font-weight-bold">
                                    {{ $user->created_at 
                                        ? $user->created_at->format('d F Y, H:i') 
                                        : '-' }}
                                </div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="small text-muted">Terakhir Update</label>
                                <div class="font-weight-bold">
                                    {{ $user->updated_at 
                                        ? $user->updated_at->format('d F Y, H:i') 
                                        : '-' }}
                                </div>
                            </div>
                            @if ($user->roles == 'KONTRIBUTOR' || $user->roles == 'VALIDATOR')
                                <div class="info-item mb-3">
                                    <label class="small text-muted">Kontributor Spesialis</label>
                                    <div class="font-weight-bold">
                                        {{ $user->is_validator ? 'Ya' : 'Tidak' }}
                                    </div>
                                </div>

                                <div class="info-item mb-3">
                                    <label class="small text-muted">Spesialisasi Mata Pelajaran</label>
                                    <div class="font-weight-bold">
                                        {{ $user->subCategory ? $user->subCategory->name : '-' }}
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Kolom Kanan -->
                        <div class="col-md-6">
                            <div class="info-item mb-3">
                                <label class="small text-muted">Sekolah Terakhir</label>
                                <div class="font-weight-bold">{{ $user->sekolah_terakhir ?? '-' }}</div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="small text-muted">Alamat</label>
                                <div class="font-weight-bold">{{ $user->alamat ?? '-' }}</div>
                            </div>

                            {{-- Social Media --}}
                            <div class="info-item mb-3">
                                <label class="small text-muted">Instagram</label>
                                <div class="font-weight-bold">
                                    @if($user->instagram)
                                        <a href="https://instagram.com/{{ $user->instagram }}" target="_blank">
                                            {{ '@' . $user->instagram }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="small text-muted">LinkedIn</label>
                                <div class="font-weight-bold">
                                    @if($user->linkedin)
                                        <a href="{{ $user->linkedin }}" target="_blank">
                                            {{ parse_url($user->linkedin, PHP_URL_HOST) }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            <div class="info-item mb-3">
                                <label class="small text-muted">Twitter</label>
                                <div class="font-weight-bold">
                                    @if($user->twitter)
                                        <a href="https://twitter.com/{{ $user->twitter }}" target="_blank">
                                            {{ '@' . $user->twitter }}
                                        </a>
                                    @else
                                        -
                                    @endif
                                </div>
                            </div>
                            {{-- Akhir Social Media --}}

                            {{-- Daftar Jurusan Pilihan --}}
                            <div class="info-item mb-3">
                                <label class="small text-muted">Jurusan Pilihan</label>
                                <div class="font-weight-bold">
                                    @if($user->userMajor->isEmpty())
                                        -
                                    @else
                                        <ul class="mb-0 pl-3">
                                            @foreach($user->userMajor as $um)
                                                <li>{{ $um->major->name }}</li><span> {{ $um->major->university->name }}</span>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>

                            {{-- Akhir Jurusan --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tombol Kembali -->
    <div class="row">
        <div class="col-12">
            <a href="{{ route('user.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
</div>
@endsection

@push('addon-style')
<style>
    /* Profile card hanya selebar konten */
    .profile-card {
        display: inline-block;
    }

    .user-avatar {
        width: 90px;
        height: 90px;
        background: linear-gradient(135deg, #4e73df, #224abe);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 28px;
        font-weight: bold;
        box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15);
        margin: 0 auto;
    }

    .info-item {
        position: relative;
        padding: 12px 0;
        border-bottom: 1px solid #e3e6f0;
    }
    .info-item:last-child {
        border-bottom: none;
    }
    .info-item label {
        letter-spacing: 0.5px;
        font-size: 11px;
    }
    .info-item div {
        font-size: 14px;
        color: #5a5c69;
        font-weight: 500;
    }

    .card {
        border: 1px solid #e3e6f0;
        border-radius: 0.5rem;
    }
    .card-header {
        background-color: #f8f9fc;
        border-bottom: 1px solid #e3e6f0;
        border-radius: 0.5rem 0.5rem 0 0;
    }
    .shadow-sm {
        box-shadow: 0 0.15rem 1.75rem rgba(58, 59, 69, 0.15) !important;
    }
    .badge {
        font-size: 0.75rem;
    }
    .info-item a {
        color: #4e73df;
        text-decoration: none;
    }
    .info-item a:hover {
        text-decoration: underline;
    }
</style>
@endpush
