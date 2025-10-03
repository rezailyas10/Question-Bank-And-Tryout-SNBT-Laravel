@extends('layouts.sales')

@section('title')
  Daftar Pendaftar Mahasiswa ITI
@endsection
<style>
    .status-published {
  background-color: #28a7451a;
  color: #28a745;
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
}

.status-unpublished {
  background-color: #ffc1071a;
  color: #ffc107;
  padding: 4px 8px;
  border-radius: 4px;
  font-weight: bold;
}

.filter-container {
    background: white;
    border-radius: 12px;
    padding: 24px;
    margin-bottom: 24px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
}

.filter-row {
    display: flex;
    gap: 20px;
    align-items: end;
    flex-wrap: wrap;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.filter-label {
    display: block;
    font-weight: 600;
    color: #374151;
    margin-bottom: 8px;
    font-size: 0.9em;
}

.filter-select, .filter-input {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e5e7eb;
    border-radius: 8px;
    font-size: 0.95em;
    transition: all 0.2s ease;
    background: white;
}

.filter-select:focus, .filter-input:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filter-actions {
    display: flex;
    gap: 12px;
}

.btn-filter-apply {
    background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: all 0.2s ease;
}

.btn-filter-apply:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

.btn-filter-reset {
    background: #f3f4f6;
    color: #6b7280;
    border: 2px solid #e5e7eb;
    padding: 12px 24px;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.2s ease;
}

.btn-filter-reset:hover {
    background: #e5e7eb;
    color: #374151;
}
/* Pagination Container */
.pagination-container {
    margin-top: 32px;
    display: flex;
    justify-content: center;
}


</style>
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
        <p class="stat-number">{{ $totalRegistrations }}</p>
        <p class="stat-label">Total Pendaftar</p>
    </div>
    <div class="stat-card">
        <p class="stat-number">{{ $contactedCount }}</p>
        <p class="stat-label">Sudah Dihubungi</p>
    </div>
    <div class="stat-card">
        <p class="stat-number">{{ $notContactedCount }}</p>
        <p class="stat-label">Belum Dihubungi</p>
    </div>
</div>
 <!-- Filter Section -->
    <div class="filter-container">
        <form method="GET" action="{{ route('pendaftar.index') }}">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="search" class="filter-label">Cari Pendaftar</label>
                    <input type="text" name="search" id="search" class="filter-input" value="{{ request('search') }}" placeholder="Masukkan nama program studi...">
                </div>
                
                <div class="filter-actions">
                    <button type="submit" class="btn-filter-apply">
                        <svg width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z"/>
                        </svg>
                        Terapkan
                    </button>
                    <a href="{{ route('pendaftar.index') }}" class="btn-filter-reset">Reset</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Success Notification -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Table Container -->
   <div class="table-container">
  <div class="table-header">
    <h1>Daftar Pendaftaran Kampus</h1>
  </div>

  @if($registrations->count() > 0)
  <table class="enhanced-table">
    <thead>
      <tr>
        <th>Nama</th>
        <th>Username</th>
        <th>Program Studi</th>
        <th>Periode Akademik</th>
        <th>Status Kontak</th>
        <th>Waktu Daftar</th>
        <th>Aksi</th>
      </tr>
    </thead>
    <tbody>
      @foreach($registrations as $reg)
      <tr>
        <td><strong>{{ $reg->result->user->name ?? '-' }}</strong></td>

        <td>{{ $reg->result->user->username ?? '-' }}</td>

        <td><span class="exam-type-badge">{{ $reg->program_studi }}</span></td>

        <td>{{ $reg->periode_akademik }}</td>

        <td>
          <span class="status-badge {{ $reg->status === 'sudah dihubungi' ? 'status-published' : 'status-unpublished' }}">
            {{ ucfirst($reg->status) }}
          </span>
        </td>

        <td>{{ $reg->created_at->diffForHumans() }}</td>

        <td>
          <div class="action-buttons">
            <a href="{{ route('pendaftar.edit', $reg->id) }}" class="btn-modern btn-detail">
              <svg width="12" height="12" fill="currentColor" viewBox="0 0 16 16">
                <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8zM1.173 8a13.133 13.133 0 0 1 1.66-2.043C4.12 4.668 5.88 3.5 8 3.5c2.12 0 3.879 1.168 5.168 2.457A13.133 13.133 0 0 1 14.828 8c-.058.087-.122.183-.195.288-.335.48-.83 1.12-1.465 1.755C11.879 11.332 10.119 12.5 8 12.5c-2.12 0-3.879-1.168-5.168-2.457A13.134 13.134 0 0 1 1.172 8z"/>
                <path d="M8 5.5a2.5 2.5 0 1 0 0 5 2.5 2.5 0 0 0 0-5zM4.5 8a3.5 3.5 0 1 1 7 0 3.5 3.5 0 0 1-7 0z"/>
            </svg>
              Detail
            </a>
          </div>
        </td>
      </tr>
      @endforeach
    </tbody>
  </table>
  @else
  <div class="empty-state">
    <div class="empty-state-icon">📋</div>
    <h3>Belum ada pendaftaran</h3>
    <p>Data pendaftaran siswa akan muncul di sini.</p>
  </div>
  @endif
</div>
 <!-- Pagination -->
    @if($registrations->hasPages())
    <div class="pagination-container">
        {{ $registrations->links() }}
    </div>
    @endif
</div>
@endsection