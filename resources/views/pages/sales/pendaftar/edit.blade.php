@extends('layouts.sales')

@section('title')
  Detail Pendaftar ITI
@endsection

@section('content')
<div class="section-content section-dashboard-home" data-aos="fade-up">
    <div class="container-fluid">
        <div class="dashboard-heading">
            <h2 class="dashboard-title">Detail Pendaftar ITI</h2>
            <p class="dashboard-subtitle">Informasi lengkap pendaftar dan status follow-up</p>
        </div>
        
        <div class="dashboard-content">
            <div class="row">
                <div class="col-md-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    <!-- Informasi Pendaftar -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Pendaftar</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-group mb-3">
                                        <label class="info-label">Nama Lengkap</label>
                                        <div class="info-value">{{ $registration->result->user->name }}</div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="info-label">Email</label>
                                        <div class="info-value">{{ $registration->result->user->email }}</div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="info-label">Nomor Telepon</label>
                                        <div class="info-value">{{ $registration->result->user->phone_number ?? '-' }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="info-group mb-3">
                                        <label class="info-label">Skor Ujian</label>
                                        <div class="info-value">{{ $registration->result->score }}</div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="info-label">Ujian</label>
                                        <div class="info-value">{{ $registration->result->exam->title }}</div>
                                    </div>
                                    <div class="info-group mb-3">
                                        <label class="info-label">Tanggal Daftar</label>
                                        <div class="info-value">{{ $registration->created_at->format('d M Y H:i') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Rekomendasi AI -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Program Studi yang Direkomendasikan oleh AI</h5>
                        </div>
                        <div class="card-body">
                            <div class="recommendations-list">
                                @foreach($recommendedMajors as $major)
                                    @php
                                        $cleaned = trim(strip_tags(is_array($major) ? implode('', $major) : $major));
                                    @endphp
                                    @if($cleaned !== '')
                                        <div class="recommendation-item">
                                            {{ $cleaned }}
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Informasi Pendaftaran -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Informasi Pendaftaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="info-group mb-3">
                                        <label class="info-label">Periode Akademik</label>
                                        <div class="info-value">{{ $registration->periode_akademik }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-group mb-3">
                                        <label class="info-label">Program Studi</label>
                                        <div class="info-value">{{ $registration->program_studi }}</div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="info-group mb-3">
                                        <label class="info-label">Bersedia Dihubungi</label>
                                        <div class="info-value">
                                            @if($registration->agree_to_contact)
                                                <span class="badge badge-success">Ya</span>
                                            @else
                                                <span class="badge badge-secondary">Tidak</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form Edit Status dan Keterangan -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Update Status Follow-up</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ route('pendaftar.update', $registration->id) }}" method="POST">
                                @method('PUT')
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">Status</label>
                                            <select name="status" id="status" class="form-control" required>
                                                <option value="belum dihubungi" {{ $registration->status == 'belum dihubungi' ? 'selected' : '' }}>
                                                    Belum Dihubungi
                                                </option>
                                                <option value="sudah dihubungi" {{ $registration->status == 'sudah dihubungi' ? 'selected' : '' }}>
                                                    Sudah Dihubungi
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="keterangan">Keterangan</label>
                                            <textarea name="keterangan" id="keterangan" class="form-control" rows="3" 
                                                placeholder="Tambahkan keterangan atau catatan follow-up...">{{ $registration->keterangan }}</textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-12">
                                        <div class="d-flex justify-content-between">
                                            <a href="{{ route('pendaftar.index') }}" class="btn btn-secondary">
                                                Kembali
                                            </a>
                                            <button type="submit" class="btn btn-primary">
                                                Update Status
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.info-group {
    border-bottom: 1px solid #f1f1f1;
    padding-bottom: 0.5rem;
}

.info-label {
    font-weight: 600;
    color: #6c757d;
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    display: block;
}

.info-value {
    color: #495057;
    font-weight: 500;
}

.recommendations-list {
    display: grid;
    gap: 0.75rem;
}

.recommendation-item {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 6px;
    padding: 0.75rem 1rem;
    color: #495057;
    font-weight: 500;
}

.badge {
    font-size: 0.75rem;
    padding: 0.375rem 0.75rem;
}

.badge-success {
    background-color: #28a745;
    color: white;
}

.badge-secondary {
    background-color: #6c757d;
    color: white;
}

.card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.card-header {
    background-color: #f8f9fa;
    border-bottom: 1px solid #e9ecef;
    padding: 1rem 1.25rem;
}

.card-title {
    color: #495057;
    font-weight: 600;
}

.form-control, .form-select {
    border: 1px solid #ced4da;
    border-radius: 4px;
}

.form-control:focus, .form-select:focus {
    border-color: #80bdff;
    box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
}

.btn {
    border-radius: 4px;
    font-weight: 500;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
}
</style>

@endsection