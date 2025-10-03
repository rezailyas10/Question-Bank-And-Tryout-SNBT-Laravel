@php
    switch (auth()->user()->roles) {
        case 'ADMIN':
            $layout = 'layouts.admin';
            break;
        case 'KONTRIBUTOR':
            $layout = 'layouts.kontributor';
            break;
        case 'VALIDATOR':
            $layout = 'layouts.validator';
            break;
        default:
            $layout = 'layouts.app'; // fallback
    }
@endphp

@extends($layout)

@section('title', 'Import Soal')

@section('content')
<div class="container-fluid px-4">
    <h2 class="mb-4">Upload Soal dari Excel</h2>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Error Messages --}}
    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">Import File Excel</h5>
                </div>
                <div class="card-body">
                    {{-- Download Template Button --}}
                    <div class="mb-3">
                        <a href="{{ route('questions.download-template') }}" class="btn btn-success">
                            <i class="fas fa-download me-1"></i> Download Template Excel
                        </a>
                        <small class="text-muted d-block mt-1">
                            Download template terlebih dahulu untuk format yang sesuai
                        </small>
                    </div>

                    <hr>

                    {{-- Import Form --}}
                    <form action="{{ route('questions.import', ['exam_id' => $exam->id]) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="file" class="form-label">Pilih File Excel</label>
                            <input type="file" 
                                   name="file" 
                                   id="file" 
                                   class="form-control @error('file') is-invalid @enderror" 
                                   accept=".xlsx,.xls" 
                                   required>
                            @error('file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Format yang didukung: .xlsx, .xls</small>
                        </div>
                        
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-upload me-1"></i> Import Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Info Panel --}}
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h6 class="card-title mb-0">Informasi Ujian</h6>
                </div>
                <div class="card-body">
                    <table class="table table-sm">
                        <tr>
                            <td><strong>Nama Ujian:</strong></td>
                            <td>{{ $exam->title }}</td>
                        </tr>
                        <tr>
                            <td><strong>Tipe:</strong></td>
                            <td>
                                <span class="badge bg-info">{{ ucfirst($exam->exam_type) }}</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Mata Pelajaran:</strong></td>
                            <td>{{ $exam->subCategory->name ?? 'Gabungan' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h6 class="card-title mb-0">Petunjuk Import</h6>
                </div>
                <div class="card-body">
                    <ol class="small">
                        <li>Download template Excel terlebih dahulu</li>
                        <li>Isi soal sesuai format yang tersedia</li>
                        <li>Pastikan semua kolom wajib terisi</li>
                        <li>Upload file yang sudah diisi</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Back Button --}}
    <div class="mt-4">
        @if ($exam->exam_type == 'latihan soal')
            <a href="{{ route('exam.show', $exam->slug) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Ujian
            </a>
        @else
            <a href="{{ route('tryout.show', $exam->slug) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali ke Ujian
            </a>
        @endif
    </div>
</div>
@endsection

@push('styles')
<style>
.card {
    border: 1px solid #e3e6f0;
    border-radius: 0.35rem;
}

.card-header {
    background-color: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
}

.alert {
    border-radius: 0.35rem;
}

.btn {
    border-radius: 0.35rem;
}

.form-control {
    border-radius: 0.35rem;
}

.table-sm td {
    padding: 0.3rem;
    vertical-align: middle;
}
</style>
@endpush