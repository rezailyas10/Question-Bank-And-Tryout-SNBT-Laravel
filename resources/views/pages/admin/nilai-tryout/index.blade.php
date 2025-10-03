@extends('layouts.admin')

@section('title')
  Daftar Pengerjaan Tryout
@endsection
<link rel="stylesheet" href="{{ asset('/style/kontributor-exam.css') }}?v={{ time() }}">
@section('content')
<div class="container">
    <h1>Daftar Tryout</h1>

    <!-- Notifikasi sukses -->
    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

   <div class="container">
    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <p class="stat-number">{{ $exams->count() }}</p>
            <p class="stat-label">Total Tryout</p>
        </div>
        <div class="stat-card">
            <p class="stat-number">{{ $exams->where('is_published', true)->count() }}</p>
            <p class="stat-label">Published</p>
        </div>
        <div class="stat-card">
            <p class="stat-number">{{ $exams->sum(function($exam) { return $exam->questions->count(); }) }}</p>
            <p class="stat-label">Total Pertanyaan</p>
        </div>
        <div class="stat-card">
            <p class="stat-number">{{ $exams->sum('results_count') }}</p>
            <p class="stat-label">Total Peserta</p>
        </div>
    </div>

    <!-- Success Notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('nilai-tryout.index') }}" id="filterForm">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="created_by" class="filter-label">Filter Pembuat</label>
                    <select name="created_by" id="created_by" class="filter-select">
                        <option value="">-- Semua Pembuat --</option>
                        <option value="{{ Auth::user()->name }}" {{ request('created_by') == Auth::user()->name ? 'selected' : '' }}>
                            {{ Auth::user()->name }}
                        </option>
                    </select>
                </div>
                                
                <div class="filter-group">
                    <label for="search" class="filter-label">Cari Tryout</label>
                    <input type="text" name="search" id="search" class="filter-input" value="{{ request('search') }}" placeholder="Masukkan nama Tryout...">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-filter-apply">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Terapkan
                    </button>
                    <a href="{{ route('nilai-tryout.index') }}" class="btn-filter-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>


    <!-- Table Container -->
    <div class="table-container">
        <div class="table-header">
            <h1>Daftar Tryout</h1>
        </div>

        @if($exams->count() > 0)
        <table class="enhanced-table">
            <thead>
                <tr>
                    <th>Nama Tryout</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Pertanyaan</th>
                    <th>Peserta</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($exams as $exam)
                <tr>
                    <td>
                        <strong>{{ $exam->title }}</strong>
                    </td>
                    <td>
                        <span class="status-badge {{ $exam->is_published ? 'status-published' : 'status-unpublished' }}">
                            {{ $exam->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td>
                        <div class="creator-info">
                            <div class="creator-avatar">
                                {{ strtoupper(substr($exam->created_by, 0, 1)) }}
                            </div>
                            <span>{{ $exam->created_by }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="question-count">{{ $exam->questions->count() }}</span>
                    </td>
                    <td>
                        <span class="participant-count">{{ $exam->results_count ?? 0 }}</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('nilai-tryout.show', $exam->slug) }}" class="btn-modern btn-analysis">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M1.5 1a.5.5 0 0 0-.5.5v4a.5.5 0 0 1-1 0v-4A1.5 1.5 0 0 1 1.5 0h4a.5.5 0 0 1 0 1h-4zM11 .5a.5.5 0 0 1 .5-.5h4A1.5 1.5 0 0 1 17 1.5v4a.5.5 0 0 1-1 0v-4a.5.5 0 0 0-.5-.5h-4a.5.5 0 0 1-.5-.5zM.5 11a.5.5 0 0 1 .5.5v4a.5.5 0 0 0 .5.5h4a.5.5 0 0 1 0 1h-4A1.5 1.5 0 0 1 0 15.5v-4a.5.5 0 0 1 .5-.5zm15 0a.5.5 0 0 1 .5.5v4a1.5 1.5 0 0 1-1.5 1.5h-4a.5.5 0 0 1 0-1h4a.5.5 0 0 0 .5-.5v-4a.5.5 0 0 1 .5-.5z"/>
                                    <path d="M3 3h10a2 2 0 0 1 2 2v6a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2z"/>
                                </svg>
                                Analisa IRT
                            </a>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📊</div>
            <h3>Belum ada Tryout</h3>
            <p>Mulai dengan membuat Tryout pertama untuk analisa IRT</p>
            <a href="{{ route('nilai-tryout.create') }}" class="btn-create" style="margin-top: 20px;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                Buat Tryout Pertama
            </a>
        </div>
        @endif
    </div>
    <!-- Pagination -->
    @if($exams->hasPages())
    <div class="pagination-container">
        {{ $exams->links() }}
    </div>
    @endif
</div>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const filterForm = document.getElementById('filterForm');
        const selects = filterForm.querySelectorAll('select');

        selects.forEach(function(select) {
            select.addEventListener('change', function () {
                filterForm.submit();
            });
        });
    });
</script>

<style>
/* Additional styles for the new elements */

.participant-count {
    background: #e3f2fd;
    color: #1976d2;
    padding: 4px 8px;
    border-radius: 12px;
    font-size: 0.85em;
    font-weight: 500;
}

.btn-analysis {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-analysis:hover {
    background: linear-gradient(135deg, #5a67d8 0%, #6b46c1 100%);
    color: white !important;
    transform: translateY(-1px);
}


.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
}
</style>
@endsection