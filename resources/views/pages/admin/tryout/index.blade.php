@extends('layouts.admin')

@section('title')
  Tryout
@endsection

<link rel="stylesheet" href="{{ asset('/style/kontributor-exam.css') }}?v={{ time() }}">

@section('content')

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="container">
    <!-- Stats Cards -->
    <div class="stats-row">
        <div class="stat-card">
            <p class="stat-number">{{ $exams->count() }}</p>
            <p class="stat-label">Total tryout</p>
        </div>
        <div class="stat-card">
            <p class="stat-number">{{ $exams->where('is_published', true)->count() }}</p>
            <p class="stat-label">Published</p>
        </div>
        <div class="stat-card">
            <p class="stat-number">{{ $exams->sum(function($exam) { return $exam->questions->count(); }) }}</p>
            <p class="stat-label">Total Pertanyaan</p>
        </div>
    </div>

    <!-- Success Notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Create Button -->
    <a href="{{ route('tryout.create') }}" class="btn-create">
        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
            <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
        </svg>
        Buat tryout Baru
    </a>

     <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('tryout.index') }}" id="filterForm">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="user_id" class="filter-label">Filter Pembuat</label>
                    <select name="user_id" id="user_id" class="filter-select">
                        <option value="">-- Semua Pembuat --</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
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
                    <a href="{{ route('tryout.index') }}" class="btn-filter-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-header">
            <h1>Daftar tryout</h1>
        </div>

        @if($exams->count() > 0)
        <table class="enhanced-table">
            <thead>
                <tr>
                    <th>Nama tryout</th>
                    <th>Status</th>
                    <th>Dibuat Oleh</th>
                    <th>Tipe Ujian</th>
                    <th>Pertanyaan</th>
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
                                {{ strtoupper(substr($exam->user->name, 0, 1)) }}
                            </div>
                            <span>{{ $exam->user->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="exam-type-badge">{{ $exam->exam_type }}</span>
                    </td>
                    <td>
                        <span class="question-count">{{ $exam->questions->count() }}</span>
                    </td>
                    <td>
                        <div class="action-buttons">
                            <a href="{{ route('tryout.show', $exam->slug) }}" class="btn-modern btn-detail">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                                    <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
                                </svg>
                                Detail
                            </a>
                        @if($exam->user_id == Auth::user()->id)
                            <a href="{{ route('tryout.edit', $exam->id) }}" class="btn-modern btn-edit">
                                <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                    <path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708L10.5 8.207l-3-3L12.146.146zM11.207 9l-3-3-6.364 6.364a.5.5 0 0 0-.146.353V14.5a.5.5 0 0 0 .5.5h1.793a.5.5 0 0 0 .353-.146L11.207 9z"/>
                                </svg>
                                Edit
                            </a>
                            <form action="{{ route('tryout.destroy', $exam->id) }}" method="POST" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-modern btn-delete" onclick="return confirm('Apakah anda yakin ingin menghapus tryout ini?')">
                                    <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                                        <path d="M6.5 1h3a.5.5 0 0 1 .5.5v1H6v-1a.5.5 0 0 1 .5-.5ZM11 2.5v-1A1.5 1.5 0 0 0 9.5 0h-3A1.5 1.5 0 0 0 5 1.5v1H2.506a.58.58 0 0 0-.01 0H1.5a.5.5 0 0 0 0 1h.538l.853 10.66A2 2 0 0 0 4.885 16h6.23a2 2 0 0 0 1.994-1.84l.853-10.66h.538a.5.5 0 0 0 0-1h-.995a.59.59 0 0 0-.01 0H11Zm1.958 1-.846 10.58a1 1 0 0 1-.997.92h-6.23a1 1 0 0 1-.997-.92L3.042 3.5h9.916Zm-7.487 1a.5.5 0 0 1 .528.47l.5 8.5a.5.5 0 0 1-.998.06L5 5.03a.5.5 0 0 1 .47-.53Zm5.058 0a.5.5 0 0 1 .47.53l-.5 8.5a.5.5 0 1 1-.998-.06l.5-8.5a.5.5 0 0 1 .528-.47ZM8 4.5a.5.5 0 0 1 .5.5v8.5a.5.5 0 0 1-1 0V5a.5.5 0 0 1 .5-.5Z"/>
                                    </svg>
                                    Hapus
                                </button>
                            </form>
                        @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @else
        <div class="empty-state">
            <div class="empty-state-icon">📚</div>
            <h3>Belum ada tryout</h3>
            <p>Mulai dengan membuat tryout pertama Anda</p>
            <a href="{{ route('tryout.create') }}" class="btn-create" style="margin-top: 20px;">
                <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z"/>
                </svg>
                Buat tryout Pertama
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
@endsection